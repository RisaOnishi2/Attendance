<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_admin', false)->get();
        $month = Carbon::create(2025, 10, 1); // 2025年10月

        foreach ($users as $user) {
            for ($day = 0; $day < $month->daysInMonth; $day++) {
                $date = $month->copy()->addDays($day);

                // 既に同日データがある場合はスキップ
                $attendance = Attendance::where('user_id', $user->id)
                    ->whereDate('date', $date)
                    ->first();

                if ($attendance) {
                    continue;
                }

                // 日曜日は休日
                if ($date->isSunday()) {
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date,
                        'work_status' => 'off',
                        'note' => '休日',
                        'approval_status' => 'approved',
                    ]);
                    continue;
                }

                // 承認状態をランダムに（通常勤務 or 修正申請中）
                $approvalStatus = collect(['approved', 'pending'])->random();

                // 出退勤時間をランダム生成
                $clockIn = Carbon::createFromTime(rand(8, 9), rand(0, 59));
                $clockOut = (clone $clockIn)->addHours(rand(8, 9))->addMinutes(rand(0, 30));

                $note = '通常勤務';
                $pendingData = null;

                // 承認待ちの場合は修正申請データを作成
                if ($approvalStatus === 'pending') {
                    $type = collect(['遅刻', '早退', '打刻漏れ'])->random();

                    switch ($type) {
                        case '遅刻':
                            $pendingClockIn = (clone $clockIn)->addMinutes(rand(10, 60));
                            $note = '遅刻のため修正申請';
                            break;

                        case '早退':
                            $pendingClockOut = (clone $clockOut)->subMinutes(rand(30, 90));
                            $note = '早退のため修正申請';
                            break;

                        case '打刻漏れ':
                            $pendingClockIn = null;
                            $pendingClockOut = null;
                            $note = '打刻漏れ修正申請';
                            break;
                    }

                    $pendingData = [
                        'clock_in_time' => $pendingClockIn ?? $clockIn,
                        'clock_out_time' => $pendingClockOut ?? $clockOut,
                        'note' => $note,
                    ];
                }

                // 勤怠データ登録（存在すれば更新）
                $attendance = Attendance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'date' => $date,
                    ],
                    [
                        'work_status' => 'working',
                        'clock_in_time' => $clockIn,
                        'clock_out_time' => $clockOut,
                        'note' => $note,
                        'approval_status' => $approvalStatus,
                        'pending_data' => $pendingData,
                        'requested_at' => $approvalStatus === 'pending' ? now() : null,
                    ]
                );

                // 休憩時間の登録（1〜2件ランダム）
                $breakCount = rand(1, 2);
                $breakStart = (clone $clockIn)->addHours(rand(2, 4));

                for ($i = 0; $i < $breakCount; $i++) {
                    $start = (clone $breakStart)->addHours($i * 2);
                    $end = (clone $start)->addMinutes(rand(30, 60));

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'start_time' => $start,
                        'end_time' => $end,
                    ]);
                }
            }
        }
    }
}
