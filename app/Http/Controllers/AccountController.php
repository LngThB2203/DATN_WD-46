<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

   public function update(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|string|max:255',
        'gender' => 'nullable|string|max:10',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
    ]);

    $user->name = $request->name;
    $user->gender = (int) $request->gender;
    $user->phone = $request->phone;
    $user->address = $request->address;

    $user->save();

    return redirect()->route('account.show')->with('success', 'Cập nhật thông tin thành công!');
}
}
