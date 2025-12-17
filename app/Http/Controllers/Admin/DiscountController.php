<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;

use App\Notifications\DiscountCreatedNotification;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Discount::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            } elseif ($request->status === 'expired') {
                $query->where('expiry_date', '<', now());
            }
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        }

        // Sort
        $sortBy    = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['code', 'discount_type', 'active', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $discounts = $query->paginate(15)->withQueryString();

        return view('admin.discounts.list', compact('discounts'));
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
    Log::info('ADMIN_DISCOUNT_STORE_HIT', [
        'payload' => $request->all(),
    ]);

    $request->validate([
        'code'            => 'required|string|max:100|unique:discounts,code',
        'discount_type'   => 'required|in:percent,fixed',
        'discount_value'  => 'required|numeric|min:0',
        'min_order_value' => 'nullable|numeric|min:0',
        'start_date'      => 'nullable|date',
        'expiry_date'     => 'nullable|date|after_or_equal:start_date',
        'usage_limit'     => 'nullable|integer|min:1',
    ]);

    try {
        $discount = Discount::create([
            'code'            => strtoupper($request->code),
            'discount_type'   => $request->discount_type,
            'discount_value'  => $request->discount_value,
            'min_order_value' => $request->min_order_value,
            'start_date'      => $request->start_date,
            'expiry_date'     => $request->expiry_date,
            'usage_limit'     => $request->usage_limit,
            'used_count'      => 0,
            'active'          => $request->has('active'),
        ]);

        // ✅ GỬI THÔNG BÁO CHO USER (PHẢI ĐẶT Ở ĐÂY)
        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            $user->notify(new DiscountCreatedNotification($discount));
        }

        Log::info('ADMIN_DISCOUNT_STORE_SUCCESS', [
            'discount_id' => $discount->id,
            'code'        => $discount->code,
        ]);

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Mã giảm giá đã được tạo thành công!');

    } catch (\Exception $e) {
        Log::error('ADMIN_DISCOUNT_STORE_ERROR', [
            'message' => $e->getMessage(),
        ]);

        return back()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
            ->withInput();
    }
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
            'code'            => 'required|string|max:100|unique:discounts,code,' . $discount->id,
            'discount_type'   => 'required|in:percent,fixed',
            'discount_value'  => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'start_date'      => 'nullable|date',
            'expiry_date'     => 'nullable|date|after_or_equal:start_date',
            'usage_limit'     => 'nullable|integer|min:1',
        ], [
            'code.required'              => 'Vui lòng nhập mã giảm giá',
            'code.unique'                => 'Mã giảm giá đã tồn tại',
            'discount_type.required'     => 'Vui lòng chọn loại giảm giá',
            'discount_value.required'    => 'Vui lòng nhập giá trị giảm giá',
            'expiry_date.after_or_equal' => 'Ngày hết hạn phải sau hoặc bằng ngày bắt đầu',
        ]);

        try {
            $discount->update([
                'code'            => strtoupper($request->code),
                'discount_type'   => $request->discount_type,
                'discount_value'  => $request->discount_value,
                'min_order_value' => $request->min_order_value,
                'start_date'      => $request->start_date,
                'expiry_date'     => $request->expiry_date,
                'usage_limit'     => $request->usage_limit,
                'active'          => $request->has('active'),
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
            'code'        => 'required|string',
            'order_value' => 'nullable|numeric|min:0',
        ]);

        $discount = Discount::where('code', strtoupper($request->code))->first();

        if (! $discount) {
            return response()->json([
                'valid'   => false,
                'message' => 'Mã giảm giá không tồn tại',
            ], 404);
        }

        $orderValue = $request->order_value ?? 0;

        if (! $discount->canApplyToOrder($orderValue)) {
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

            if ($discount->min_order_value && $orderValue < $discount->min_order_value) {
                $messages[] = 'Đơn hàng tối thiểu ' . number_format($discount->min_order_value, 0, ',', '.') . ' VNĐ';
            }

            return response()->json([
                'valid'   => false,
                'message' => implode('. ', $messages) ?: 'Mã giảm giá không thể áp dụng',
            ], 400);
        }

        $discountAmount = $discount->calculateDiscount($orderValue);
        $finalAmount    = $orderValue - $discountAmount;

        return response()->json([
            'valid'    => true,
            'discount' => [
                'id'              => $discount->id,
                'code'            => $discount->code,
                'type'            => $discount->discount_type,
                'value'           => $discount->discount_value,
                'amount'          => $discountAmount,
                'original_amount' => $orderValue,
                'final_amount'    => max(0, $finalAmount),
            ],
            'message'  => 'Áp dụng mã giảm giá thành công!',
        ]);
    }
}
