<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $discounts = Discount::valid()
            ->where('type', 'public')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $savedIds = [];
        if ($request->user()) {
            $savedIds = $request->user()->userVouchers()->pluck('discount_id')->toArray();
        }

        return view('client.vouchers.index', compact('discounts', 'savedIds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.discounts.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'code' => 'required|string|max:100|unique:discounts,code',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá đã tồn tại',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm giá',
            'expiry_date.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
        ]);

        try {
            Discount::create([
                'code' => strtoupper($request->code),
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'usage_limit' => $request->usage_limit,
                'used_count' => 0,
                'active' => $request->has('active'),
            ]);

            return redirect()->route('admin.discounts.index')->with('success', 'Mã giảm giá đã được tạo thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    public function myVouchers(Request $request)
    {
        $user = $request->user();

        $discounts = Discount::query()
            ->whereIn('id', $user->userVouchers()->pluck('discount_id'))
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        return view('client.vouchers.my', compact('discounts'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Discount $discount)
    {
        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Discount $discount)
    {
        return view('admin.discounts.edit', compact('discount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Discount $discount)
    {
        $request->validate([
            'code' => 'required|string|max:100|unique:discounts,code,' . $discount->id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
        ], [
            'code.required' => 'Vui lòng nhập mã giảm giá',
            'code.unique' => 'Mã giảm giá đã tồn tại',
            'discount_type.required' => 'Vui lòng chọn loại giảm giá',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm giá',
            'expiry_date.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
        ]);

        try {
            $discount->update([
                'code' => strtoupper($request->code),
                'discount_type' => $request->discount_type,
                'discount_value' => $request->discount_value,
                'min_order_value' => $request->min_order_value,
                'start_date' => $request->start_date,
                'expiry_date' => $request->expiry_date,
                'usage_limit' => $request->usage_limit,
                'active' => $request->has('active'),
            ]);

            return redirect()->route('admin.discounts.index')->with('success', 'Mã giảm giá đã được cập nhật thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discount $discount)
    {
        try {
            $discount->delete();
            return redirect()->route('admin.discounts.index')->with('success', 'Mã giảm giá đã được xóa thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    /**
     * Check discount code (API endpoint for checkout)
     */
    public function checkCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_value' => 'nullable|numeric|min:0',
        ]);

        $discount = Discount::where('code', strtoupper($request->code))->first();

        if (!$discount) {
            return response()->json([
                'valid' => false,
                'message' => 'Mã giảm giá không tồn tại'
            ], 404);
        }

        $orderValue = $request->order_value ?? 0;

        if (!$discount->canApplyToOrder($orderValue)) {
            $messages = [];

            if (!$discount->active) {
                $messages[] = 'Mã giảm giá đã bị vô hiệu hóa';
            }

            if ($discount->expiry_date && now()->gt($discount->expiry_date)) {
                $messages[] = 'Mã giảm giá đã hết hạn';
            }

            if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
                $messages[] = 'Mã giảm giá đã hết lượt sử dụng';
            }

            if ($discount->min_order_value && $orderValue < $discount->min_order_value) {
                $messages[] = 'Đơn hàng tối thiểu ' . number_format($discount->min_order_value, 0, ',', '.') . ' VNĐ';
            }

            return response()->json([
                'valid' => false,
                'message' => implode('. ', $messages) ?: 'Mã giảm giá không thể áp dụng'
            ], 400);
        }

        $discountAmount = $discount->calculateDiscount($orderValue);
        $finalAmount = $orderValue - $discountAmount;

        return response()->json([
            'valid' => true,
            'discount' => [
                'id' => $discount->id,
                'code' => $discount->code,
                'type' => $discount->discount_type,
                'value' => $discount->discount_value,
                'amount' => $discountAmount,
                'original_amount' => $orderValue,
                'final_amount' => max(0, $finalAmount),
            ],
            'message' => 'Áp dụng mã giảm giá thành công!'
        ]);
    }

    /**
     * Apply discount code to current cart (client side) and store in session.
     */
    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $discount = Discount::where('code', strtoupper($request->code))->first();

        if (! $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại',
            ], 404);
        }

        $sessionCart = $request->session()->get('cart', [
            'items'          => [],
            'shipping_fee'   => 30000,
            'discount_total' => 0,
        ]);

        $items = collect($sessionCart['items'] ?? [])->map(function ($item) {
            $item['quantity'] = max(1, (int) ($item['quantity'] ?? 1));
            $item['price']    = (float) ($item['price'] ?? 0);
            $item['subtotal'] = $item['quantity'] * $item['price'];
            return $item;
        });

        $subtotal    = $items->sum('subtotal');
        $shippingFee = (float) ($sessionCart['shipping_fee'] ?? 0);

        if (! $discount->canApplyToOrder($subtotal)) {
            $messages = [];

            if (! $discount->active) {
                $messages[] = 'Mã giảm giá đã bị vô hiệu hóa';
            }

            if ($discount->expiry_date && now()->gt($discount->expiry_date)) {
                $messages[] = 'Mã giảm giá đã hết hạn';
            }

            if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
                $messages[] = 'Mã giảm giá đã hết lượt sử dụng';
            }

            if ($discount->min_order_value && $subtotal < $discount->min_order_value) {
                $messages[] = 'Đơn hàng tối thiểu ' . number_format($discount->min_order_value, 0, ',', '.') . ' VNĐ';
            }

            return response()->json([
                'success' => false,
                'message' => implode('. ', $messages) ?: 'Mã giảm giá không thể áp dụng',
            ], 400);
        }

        $discountAmount = $discount->calculateDiscount($subtotal);

        $sessionCart['items']          = $items->all();
        $sessionCart['discount_id']    = $discount->id;
        $sessionCart['discount_total'] = $discountAmount;
        $sessionCart['subtotal']       = $subtotal;
        $sessionCart['grand_total']    = max(($subtotal + $shippingFee) - $discountAmount, 0);
        $sessionCart['code']           = $discount->code;

        $request->session()->put('cart', $sessionCart);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'cart'    => [
                'subtotal'       => $sessionCart['subtotal'],
                'shipping_fee'   => $shippingFee,
                'discount_total' => $sessionCart['discount_total'],
                'grand_total'    => $sessionCart['grand_total'],
                'code'           => $discount->code,
            ],
        ]);
    }

    public function saveForUser(Request $request)
    {
        $request->validate([
            'discount_id' => 'required|exists:discounts,id',
        ]);

        $user = $request->user();

        $discount = Discount::valid()
            ->where('type', 'public')
            ->find($request->discount_id);

        if (! $discount) {
            return response()->json([
                'success' => false,
                'message' => 'Voucher không khả dụng hoặc đã hết hạn.',
            ], 400);
        }

        $exists = $user->userVouchers()
            ->where('discount_id', $discount->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => true,
                'message' => 'Bạn đã lưu voucher này rồi.',
            ]);
        }

        $user->userVouchers()->create([
            'discount_id' => $discount->id,
            'saved_at'    => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu voucher vào kho của bạn.',
        ]);
    }
}
