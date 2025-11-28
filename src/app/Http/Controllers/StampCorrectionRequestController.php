<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        // 現在ログインしているユーザーID
        $userId = Auth::id();

        // クエリパラメータでタブ切り替え
        $tab = request('tab', 'pending');

        // 承認待ちと承認済みを取得（ログインユーザーのみ）
        $pending = Attendance::with('user')
            ->where('user_id', $userId)
            ->where('approval_status', 'pending')
            ->orderByDesc('requested_at')
            ->get();

        $approved = Attendance::with('user')
            ->where('user_id', $userId)
            ->where('approval_status', 'approved')
            ->whereNotNull('requested_at')
            ->orderByDesc('requested_at')
            ->get();

        return view('stamp_correction_request', compact('tab', 'pending', 'approved', 'request'));
    }
}
