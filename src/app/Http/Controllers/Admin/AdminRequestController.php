<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        // タブで承認待ち／承認済みを切り替え
        $status = $request->get('status', 'pending');

        $attendances = Attendance::with('user')
            ->when($status === 'pending', fn($q) => $q->where('approval_status', 'pending'))
            ->when($status === 'approved', fn($q) => $q->where('approval_status', 'approved'))
            ->orderByDesc('requested_at')
            ->get();

        return view('admin.requests.index', compact('attendances', 'status'));
    }
}
