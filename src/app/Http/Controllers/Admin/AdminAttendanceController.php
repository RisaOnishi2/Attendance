<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AdminAttendanceController extends Controller
{
    // 日次一覧
    public function index(Request $request)
    {
        // クエリパラメータの日付を取得。なければ今日
        $date = $request->input('date', Carbon::today()->toDateString());

        // 日付をCarbonインスタンス化
        $targetDate = Carbon::parse($date);

        // 対象日の勤怠データを全ユーザー分取得
        $attendances = Attendance::with('user')
            ->whereDate('date', $targetDate)
            ->get();

        return view('admin.attendance.index', compact('attendances', 'targetDate'));
    }

    // スタッフ別勤怠一覧
    public function show($id, Request $request)
    {
        $user = User::findOrFail($id);

        // 現在月または指定月を取得
        $currentMonth = $request->query('month')
            ? Carbon::createFromFormat('Y-m', $request->query('month'))
            : Carbon::now();

        // 月初・月末を計算
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth   = $currentMonth->copy()->endOfMonth();

        // 該当月の全勤怠データを取得
        $attendances = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(fn($attendance) => $attendance->date->format('Y-m-d')); // 日付をキーに

        // 月内の全日付リスト作成
        $dates = [];
        $date = $startOfMonth->copy();
        while ($date->lte($endOfMonth)) {
            $dates[] = $date->copy();
            $date->addDay();
        }

        return view('admin.attendance.show', compact('user', 'currentMonth', 'dates', 'attendances'));
    }
}
