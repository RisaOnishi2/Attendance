<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /** 一般ユーザーログイン画面 */
    public function showLoginForm()
    {
        return view('auth.login');
    }

      /** 管理者ログイン画面 */
    public function showAdminLoginForm()
    {
        return view('admin.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // 管理者が一般ログイン画面から入った場合の制御
            if (Auth::user()->is_admin) {
                Auth::logout();
                return back()->withErrors(['login' => '管理者は管理者専用ページからログインしてください。']);
            }

            return redirect()->intended('/attendance');
        }

        return back()->withErrors(['login' => 'ログイン情報が登録されていません。'])->withInput();
    }

    /** 管理者ログイン処理 */
    public function adminLogin(LoginRequest $request)
    {
        $credentials = $request->validated();

        // is_admin = true を条件に追加
        $credentials['is_admin'] = true;

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/attendance/list');
        }

        return back()->withErrors(['login' => '管理者アカウントが見つかりません。'])->withInput();
    }
}
