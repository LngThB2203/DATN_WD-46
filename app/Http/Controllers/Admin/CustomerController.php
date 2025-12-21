<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $customers = Customer::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhere('address', 'LIKE', "%{$search}%")
                      ->orWhere('membership_level', 'LIKE', "%{$search}%");
                });
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
        // Soft delete
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.list')->with('success', 'Khách hàng đã được xóa (có thể khôi phục)!');
    }

    public function forceDelete($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->forceDelete();

        return redirect()->route('admin.customers.trashed')->with('success', 'Khách hàng đã được xóa vĩnh viễn!');
    }

    public function restore($id)
    {
        $customer = Customer::withTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()->route('admin.customers.trashed')->with('success', 'Khách hàng đã được khôi phục!');
    }

    public function trashed(Request $request)
    {
        $query = Customer::onlyTrashed();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('deleted_at', 'desc')->paginate(15);
        $customers->appends($request->only('search'));

        return view('admin.customers.trashed', compact('customers'));
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

    public function export()
    {
        return Excel::download(new CustomersExport, 'danh_sach_khach_hang.xlsx');
    }
}
