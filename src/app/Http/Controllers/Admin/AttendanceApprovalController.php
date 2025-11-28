<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceApprovalController extends Controller
{
    /**
     * 修正申請の表示・承認処理（同一URL）
     */
    public function handle(Request $request, Attendance $attendance_correct_request)
    {
        // POST時：承認処理
        if ($request->isMethod('post')) {

            if (empty($attendance_correct_request->pending_data)) {
                return back()->with('error', '修正申請データが存在しません。');
            }

            DB::transaction(function () use ($attendance_correct_request) {
                $pending = $attendance_correct_request->pending_data;

                $attendance_correct_request->update([
                    'clock_in_time'   => $pending['clock_in_time'] ?? null,
                    'clock_out_time'  => $pending['clock_out_time'] ?? null,
                    'note'            => $pending['note'] ?? null,
                    'approval_status' => 'approved',
                    'pending_data'    => null,
                    'requested_at'    => null,
                    'approved_at'     => now(),
                ]);

                // 休憩時間を更新
                if (!empty($pending['breaks'])) {
                    $attendance_correct_request->breaks()->delete();

                    foreach ($pending['breaks'] as $break) {
                        if (!empty($break['start']) && !empty($break['end'])) {
                            // 日付だけ取り出す
                            $date = Carbon::parse($attendance_correct_request->date)->format('Y-m-d');

                            $attendance_correct_request->breaks()->create([
                                'start_time' => Carbon::parse($date . ' ' . $break['start']),
                                'end_time'   => Carbon::parse($date . ' ' . $break['end']),
                            ]);
                        }
                    }
                }
            });

            return redirect()
                ->route('admin.stamp_correction_request.approve', $attendance_correct_request->id)
                ->with('success', '申請を承認しました。');
        }

        // GET時：詳細表示
        return view('admin.stamp_correction_request.show', [
            'attendance_correct_request' => $attendance_correct_request,
        ]);
    }
}
