<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceUpdateRequest;

class AttendanceController extends Controller
{
     public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->whereDate('date', Carbon::today())
            ->first();

        // 今日の勤怠がなければ空のAttendanceモデルを作る
        if (!$attendance) {
            $attendance = new Attendance([
                'work_status' => 'off',
                'date' => Carbon::today(),
                'clock_in_time' => null,
                'clock_out_time' => null,
                'approval_status' => 'confirmed'
            ]);
        }

        return view('index', compact('attendance'));
    }

    public function clockIn(Request $request)
    {
        $user = $request->user();

        // 今日の勤怠データを取得
        $today = now()->format('Y-m-d');
        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['work_status' => 'off']
        );

        // すでに出勤済みの場合はリダイレクト
        if ($attendance->work_status !== 'off') {
            return redirect()->route('index')->with('error', '今日の出勤はすでに済んでいます');
        }

        // 出勤処理
        $attendance->work_status = 'working';
        $attendance->clock_in_time = now();
        $attendance->save();

        return redirect()->route('index')->with('success', '出勤しました');
    }


        // 休憩入
        public function startBreak(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || $attendance->work_status !== 'working') {
            return redirect()->route('index')->with('error', '休憩開始できません');
        }

        // breaks レコード作成
        $attendance->breaks()->create([
            'start_time' => now(),
        ]);

        $attendance->update(['work_status' => 'break']);

        return redirect()->route('index')->with('success', '休憩に入りました');
    }

    // 休憩戻
    public function endBreak(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || $attendance->work_status !== 'break') {
            return redirect()->route('index')->with('error', '休憩終了できません');
        }

        // 休憩中の break レコードを更新
        $break = $attendance->breaks()->whereNull('end_time')->latest()->first();
        if ($break) {
            $break->update(['end_time' => now()]);
        }

        $attendance->update(['work_status' => 'working']);

        return redirect()->route('index')->with('success', '休憩を終了しました');
    }

    // 退勤
    public function clockOut(Request $request)
    {
        $user = $request->user();
        $today = now()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // 勤務中でないと退勤できない
        if (!$attendance || $attendance->work_status !== 'working') {
            return redirect()->route('index')->with('error', '退勤できません');
        }

        // 退勤処理
        $attendance->update([
            'work_status' => 'finished',
            'clock_out_time' => now(),
        ]);

        return redirect()->route('index')->with('success', 'お疲れ様でした。');
    }

    public function list($year = null, $month = null)
    {
        // 現在の年月（指定がなければ今月）
        $current = Carbon::create($year ?? now()->year, $month ?? now()->month, 1);

        // 月初・月末
        $startOfMonth = $current->copy()->startOfMonth();
        $endOfMonth   = $current->copy()->endOfMonth();

        // ログインユーザーの勤怠データを取得
        $attendances = Attendance::with('breaks') // breaksリレーションを取得
            ->where('user_id', auth()->id())
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

         // 日付をキーにして配列に変換 & break_time・total_time を Carbon でセット
        $attendances = $attendances->mapWithKeys(function ($attendance) {
            // 休憩合計（分）
            $totalBreakMinutes = $attendance->breaks
            ? $attendance->breaks->sum(function ($break) {
                return Carbon::parse($break->start_time)->diffInMinutes(Carbon::parse($break->end_time));
            })
            : 0;

            // break_time を Carbon オブジェクトに変換（当日基準）
            $attendance->break_time = $totalBreakMinutes > 0
                ? Carbon::today()->addMinutes($totalBreakMinutes)
                : null;

            // total_time = (退勤 - 出勤) - 休憩
            if ($attendance->clock_in_time && $attendance->clock_out_time) {
                $clockIn = Carbon::parse($attendance->clock_in_time);
                $clockOut = Carbon::parse($attendance->clock_out_time);
                $totalMinutes = max($clockIn->diffInMinutes($clockOut) - $totalBreakMinutes, 0);
                $attendance->total_time = Carbon::today()->addMinutes($totalMinutes);
            } else {
                $attendance->total_time = null;
            }

            return [
                \Carbon\Carbon::parse($attendance->date)->format('Y-m-d') => $attendance
            ];
        });

        return view('list', compact('current', 'attendances'));
    }

    // 勤怠詳細
    public function show($id)
    {
        $attendance = Attendance::with(['user','breaks'])->findOrFail($id);

        // 本人 or 管理者のみ閲覧可
        if ($attendance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        // 休憩は「登録済み + 1行だけ」表示
        $breaks = $attendance->breaks;
        $breaks->push((object)[ 'start_time' => null, 'end_time' => null ]);

        return view('show', compact('attendance', 'breaks'));
    }

    // 勤怠更新（修正保存）
    public function update(AttendanceUpdateRequest $request, $id)
    {
        $attendance = Attendance::with('breaks')->findOrFail($id);

        if ($attendance->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

         // 既に承認待ちなら修正不可
        if ($attendance->approval_status === 'pending' && !Auth::user()->isAdmin()) {
            return back()->with('error', '承認待ちのため修正できません。');
        }

        // 管理者かどうかで処理を分岐
        if (Auth::user()->isAdmin()) {
            // 管理者は即時反映（承認不要）
            $attendance->update([
                'clock_in_time'  => $request->input('clock_in_time'),
                'clock_out_time' => $request->input('clock_out_time'),
                'note'           => $request->input('note'),
                'approval_status' => 'approved', // 承認済みにする
                'pending_data'    => null,       // 一時データ不要
            ]);

            // 休憩時間の更新（必要に応じて）
            if ($request->has('breaks')) {
                $attendance->breaks()->delete();
                
                foreach ($request->breaks as $break) {
                    // start と end のキー名で受け取っている
                    if (!empty($break['start']) && !empty($break['end'])) {
                        $attendance->breaks()->create([
                            'start_time' => \Carbon\Carbon::parse($attendance->date->toDateString() . ' ' . $break['start']),
                            'end_time'   => \Carbon\Carbon::parse($attendance->date->toDateString() . ' ' . $break['end']),
                        ]);
                    }
                }
            }

            return redirect()->route('show', $attendance->id)
                ->with('success', '勤怠情報を修正しました。');
        } else {
                // 修正内容を一時保存する
                $attendance->pending_data = [
                    'clock_in_time'  => $request->input('clock_in_time'),
                    'clock_out_time' => $request->input('clock_out_time'),
                    'breaks'         => $request->breaks ?? [],
                    'note'           => $request->input('note'),
                ];

                $attendance->update([
                    'requested_at' => now(),
                    'approval_status' => 'pending',
                ]);
        
                $attendance->save();

                return redirect()->route('show', $attendance->id)
                    ->with('success', '修正申請を送信しました。承認をお待ちください。');
        }
    }
}