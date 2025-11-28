<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AdminRequestController;
use App\Http\Controllers\StampCorrectionRequestController;

class RequestDispatcherController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->is_admin) {
            // 管理者の場合
            return app(AdminRequestController::class)->index($request);
        }

        // 一般ユーザーの場合
        return app(StampCorrectionRequestController::class)->index($request);
    }
}