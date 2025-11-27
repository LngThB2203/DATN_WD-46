<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Lấy các product_id được chọn
        $selectedItems = $request->input('selected_items', []);
        if (is_string($selectedItems)) {
            $selectedItems = explode(',', $selectedItems);
        }
        $selectedItems = array_filter(array_map('intval', (array)$selectedItems));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error','Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
        }

        $defaultCustomer = [
            'name' => optional($request->user())->name,
            'email' => optional($request->user())->email,
            'phone' => optional($request->user())->phone ?? null,
        ];

        return view('client.checkout', [
            'cart' => $cart,
            'defaultCustomer' => $defaultCustomer,
            'selectedItems' => $selectedItems
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'=>'required|string|max:150',
            'customer_email'=>'nullable|email|max:150',
            'customer_phone'=>'required|string|max:20',
            'shipping_province'=>'required|string|max:120',
            'shipping_district'=>'required|string|max:120',
            'shipping_ward'=>'nullable|string|max:120',
            'shipping_address_line'=>'required|string|max:255',
            'customer_note'=>'nullable|string|max:1000',
            'payment_method'=>'required|string|in:cod,bank_transfer,online'
        ]);

        $selectedItems = $request->input('selected_items', []);
        if (is_string($selectedItems)) $selectedItems = explode(',',$selectedItems);
        $selectedItems = array_filter(array_map('intval',(array)$selectedItems));

        $cart = $this->prepareCart($request, $selectedItems);

        if (empty($cart['items'])) {
            return back()->withInput()->withErrors(['cart'=>'Vui lòng chọn ít nhất một sản phẩm để thanh toán.']);
        }

        DB::beginTransaction();

        try {
            $fullAddress = $this->buildFullAddress($validated);
            $discountId = $request->session()->get('cart.discount_id');

            // Tạo đơn hàng
            $order = Order::create([
                'user_id'=>optional($request->user())->id,
                'discount_id'=>$discountId,
                'order_status'=>'pending',
                'total_price'=>$cart['subtotal'],
                'shipping_address'=>$fullAddress,
                'shipping_cost'=>$cart['shipping_fee'],
                'customer_name'=>$validated['customer_name'],
                'customer_email'=>$validated['customer_email'] ?? null,
                'customer_phone'=>$validated['customer_phone'],
                'shipping_province'=>$validated['shipping_province'],
                'shipping_district'=>$validated['shipping_district'],
                'shipping_ward'=>$validated['shipping_ward'] ?? null,
                'shipping_address_line'=>$validated['shipping_address_line'],
                'customer_note'=>$validated['customer_note'] ?? null,
                'subtotal'=>$cart['subtotal'],
                'discount_total'=>$cart['discount_total'],
                'grand_total'=>$cart['grand_total'],
                'payment_method'=>$validated['payment_method']
            ]);

            // Tạo order details
            $orderDetails = [];
            foreach($cart['items'] as $item){
                $orderDetails[] = [
                    'order_id'=>$order->id,
                    'product_id'=>$item['product_id'],
                    'variant_id'=>$item['variant_id'] ?? null,
                    'quantity'=>$item['quantity'],
                    'price'=>$item['price'],
                    'subtotal'=>$item['subtotal'],
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ];
            }
            if(!empty($orderDetails)) OrderDetail::insert($orderDetails);

            // Tạo payment
            Payment::create([
                'order_id'=>$order->id,
                'payment_method'=>$validated['payment_method'],
                'transaction_code'=>null,
                'amount'=>$cart['grand_total'],
                'status'=>'pending',
                'paid_at'=>null
            ]);

            // Nếu có discount, tăng usage
            if($discountId && $discount = Discount::find($discountId)){
                $discount->incrementUsage();
            }

            DB::commit();

            // Xóa các item đã thanh toán khỏi cart
            $this->removePaidItemsFromCart($request, $selectedItems);

            // Gửi mail an toàn
            try {
                if($order->customer_email){
                    Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
                }
            } catch (\Exception $e){
                Log::error('Send order email failed: '.$e->getMessage());
            }

            // Lưu thông tin customer vào session
            if($validated['customer_email']) $request->session()->put('last_order_email',$validated['customer_email']);
            if($validated['customer_phone']) $request->session()->put('last_order_phone',$validated['customer_phone']);

            $orderCode = '#'.str_pad((string)$order->id,6,'0',STR_PAD_LEFT);
            $successMessage = $validated['payment_method']==='bank_transfer'
                ? "Đơn hàng {$orderCode} đã được ghi nhận. Vui lòng chuyển khoản theo hướng dẫn để hoàn tất thanh toán."
                : "Đơn hàng {$orderCode} đã được ghi nhận. Chúng tôi sẽ liên hệ sớm nhất.";

            return redirect()->route('orders.index')->with('success',$successMessage);

        } catch (\Throwable $exception){
            DB::rollBack();
            Log::error('Checkout error: '.$exception->getMessage(),[
                'trace'=>$exception->getTraceAsString(),
                'request'=>$request->all()
            ]);
            return back()->withInput()->with('error','Có lỗi xảy ra khi lưu đơn hàng. Vui lòng thử lại.');
        }
    }

    /**
     * Chuẩn bị cart để hiển thị
     */
    private function prepareCart(Request $request, array $selectedItems = []): array
    {
        $sessionCart = $request->session()->get('cart',['items'=>[],'shipping_fee'=>30000,'discount_total'=>0]);
        $items = collect($sessionCart['items'] ?? []);

        if(!empty($selectedItems)){
            $items = $items->filter(fn($i)=>in_array($i['product_id'],$selectedItems,true));
        }

        $items = $items->map(function ($i) {
            $i['quantity'] = max(1, (int)($i['quantity'] ?? 1));
            $i['price'] = (float)($i['price'] ?? 0);
            $i['subtotal'] = $i['quantity'] * $i['price'];
            return $i;
        });

        $subtotal = $items->sum('subtotal');
        $shippingFee = (float)($sessionCart['shipping_fee'] ?? 0);
        $discountTotal = (float)($sessionCart['discount_total'] ?? 0);
        $grandTotal = max(($subtotal+$shippingFee)-$discountTotal,0);

        return [
            'items'=>$items->all(),
            'subtotal'=>$subtotal,
            'shipping_fee'=>$shippingFee,
            'discount_total'=>$discountTotal,
            'grand_total'=>$grandTotal
        ];
    }

    /**
     * Xây dựng địa chỉ đầy đủ
     */
    private function buildFullAddress(array $data): string
    {
        return collect([
            $data['shipping_address_line'] ?? null,
            $data['shipping_ward'] ?? null,
            $data['shipping_district'] ?? null,
            $data['shipping_province'] ?? null
        ])->filter()->implode(', ');
    }

    /**
     * Xóa các item đã thanh toán khỏi cart
     */
    private function removePaidItemsFromCart(Request $request, array $paidProductIds): void
    {
        $sessionCart = $request->session()->get('cart',['items'=>[]]);
        $sessionCart['items'] = collect($sessionCart['items'] ?? [])
            ->reject(fn($i)=>in_array($i['product_id'],$paidProductIds,true))
            ->values()
            ->all();

        // Cập nhật subtotal & grand_total
        $subtotal = collect($sessionCart['items'])->sum(fn($i)=>$i['quantity']*$i['price']);
        $sessionCart['subtotal'] = $subtotal;
        $sessionCart['grand_total'] = max(($subtotal + ($sessionCart['shipping_fee'] ?? 0)) - ($sessionCart['discount_total'] ?? 0),0);

        $request->session()->put('cart',$sessionCart);

        // Nếu user đăng nhập, xóa DB CartItem tương ứng
        $user = $request->user();
        if($user){
            CartItem::whereIn('product_id',$paidProductIds)->delete();
        }
    }
}
