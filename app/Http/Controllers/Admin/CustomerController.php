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
    ->whereHas('user', function ($q) {
            $q->where('role', 'user');
        })
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

   public function store(Request $request)
{
    $request->validate([
        'user_id'           => 'required|exists:users,id',
        'address'           => 'nullable|string|max:255',
        'gender'            => 'nullable|in:Nam,Nữ,Khác',
        'membership_level'  => 'required|in:Silver,Gold,Platinum',
    ]);

    return redirect()->route('admin.customers.list')
        ->with('success', 'Thêm khách hàng thành công!');
}


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
