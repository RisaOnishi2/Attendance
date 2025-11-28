<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class StaffController extends Controller
{
    public function index()
    {
        // 管理者を除く一般スタッフを取得
        $users = User::where('is_admin', false)
                     ->select('id', 'name', 'email')
                     ->get();

        return view('admin.staff.index', compact('users'));
    }
}
