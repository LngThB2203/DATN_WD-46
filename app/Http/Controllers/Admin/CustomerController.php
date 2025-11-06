<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $customers = Customer::when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.customers.list', compact('customers', 'search'));
    }

    /**
     * Form thêm khách hàng
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Lưu khách hàng mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:customers,email',
            'phone'             => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:255',
            'gender'            => 'nullable|in:Nam,Nữ,Khác',
            'membership_level'  => 'required|in:Silver,Gold,Platinum',
            'status'            => 'boolean',
        ]);

        Customer::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'gender'            => $request->gender,
            'membership_level'  => $request->membership_level,
            'status'            => $request->status ?? 1,
        ]);

        return redirect()->route('admin.customers.list')->with('success', 'Thêm khách hàng thành công!');
    }

    /**
     * Form sửa khách hàng
     */
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Cập nhật khách hàng
     */
    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:customers,email,' . $customer->id,
            'phone'             => 'nullable|string|max:15',
            'address'           => 'nullable|string|max:255',
            'gender'            => 'nullable|in:Nam,Nữ,Khác',
            'membership_level'  => 'required|in:Silver,Gold,Platinum',
            'status'            => 'boolean',
        ]);

        $customer->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'gender'            => $request->gender,
            'membership_level'  => $request->membership_level,
            'status'            => $request->status ?? 1,
        ]);

        return redirect()->route('admin.customers.list')->with('success', 'Cập nhật khách hàng thành công!');
    }

    /**
     * Xóa khách hàng
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.list')->with('success', 'Xóa khách hàng thành công!');
    }

    /**
     * Chuyển đổi trạng thái (bật/tắt)
     */
    public function toggle($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = !$customer->status;
        $customer->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
