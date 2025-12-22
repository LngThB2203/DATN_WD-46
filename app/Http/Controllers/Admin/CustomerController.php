<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Exports\CustomersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
       $search = $request->input('search');

    $customers = Customer::with('user')
        ->when($search, function ($query, $search) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            })->orWhere('membership_level', 'LIKE', "%{$search}%");
        })
        ->orderByDesc('id')
        ->paginate(10);

        return view('admin.customers.list', compact('customers', 'search'));
    }


    /**
     * Lưu khách hàng mới
     */
   public function store(Request $request)
{
    $request->validate([
        'user_id'           => 'required|exists:users,id',
        'address'           => 'nullable|string|max:255',
        'gender'            => 'nullable|in:Nam,Nữ,Khác',
        'membership_level'  => 'required|in:Silver,Gold,Platinum',
    ]);

    Customer::create([
        'user_id'           => $request->user_id,
        'address'           => $request->address,
        'gender'            => $request->gender,
        'membership_level'  => $request->membership_level,
    ]);

    return redirect()->route('admin.customers.list')
        ->with('success', 'Thêm khách hàng thành công!');
}


    /**
     * Cập nhật khách hàng
     */
   public function update(Request $request, $id)
{
    $customer = Customer::findOrFail($id);

    $request->validate([
        'address'           => 'nullable|string|max:255',
        'gender'            => 'nullable|in:Nam,Nữ,Khác',
        'membership_level'  => 'required|in:Silver,Gold,Platinum',
    ]);

    $customer->update([
        'address'           => $request->address,
        'gender'            => $request->gender,
        'membership_level'  => $request->membership_level,
    ]);

    return redirect()->route('admin.customers.list')
        ->with('success', 'Cập nhật khách hàng thành công!');
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
        $user->status = 0;
        $customer->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function export()
    {
        return Excel::download(new CustomersExport, 'danh_sach_khach_hang.xlsx');
    }
    public function toggleUser(Customer $customer)
{
    $user = $customer->user;
    if ($user->id === auth()->id()) {
        return redirect()->back()
            ->with('error', 'Bạn không thể tự khóa tài khoản của mình');
    }

    $user->status = $user->status == 1 ? 0 : 1;
    $user->save();

    return redirect()->back()->with(
        'success',
        $user->status == 1
            ? 'Mở tài khoản thành công'
            : 'Khóa tài khoản thành công'
    );
}

}
