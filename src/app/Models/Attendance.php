<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'work_status',
        'clock_in_time',
        'clock_out_time',
        'note',
        'pending_data',
        'approval_status',
        'requested_at',
    ];

    protected $casts = [
        'pending_data' => 'array',
        'date' => 'date',
        'clock_in_time' => 'datetime',
        'clock_out_time' => 'datetime',
        'requested_at' => 'datetime',
    ];

    protected $dates = ['date', 'clock_in_time', 'clock_out_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id', 'id');
    }

    // アクセサ（休憩時間合計を計算）
    public function getBreakTimeAttribute()
    {
        $total = 0;

        foreach ($this->breaks as $break) {
            if ($break->start_time && $break->end_time) {
                // start_time, end_time が datetime にキャストされている
                $total += $break->end_time->diffInMinutes($break->start_time);
            }
        }

        // 分を「時間:分」形式に変換
        $hours = floor($total / 60);
        $minutes = $total % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getWorkDurationAttribute()
    {
        // 出勤・退勤がどちらもなければ計算しない
        if (!$this->clock_in_time || !$this->clock_out_time) {
            return null;
        }

         // 出勤・退勤をCarbonに変換
        $clockIn  = Carbon::parse($this->clock_in_time);
        $clockOut = Carbon::parse($this->clock_out_time);

        // 総労働分数
        $totalMinutes = $clockOut->diffInMinutes($clockIn);

        // 休憩時間の合計（分）
        $breakMinutes = 0;
        foreach ($this->breaks as $break) {
            if ($break->start_time && $break->end_time) {
                // start_time, end_time が datetime にキャストされている想定
                $breakMinutes += $break->end_time->diffInMinutes($break->start_time);
            }
        }

        // 実働時間（分）
        $workMinutes = $totalMinutes - $breakMinutes;

        // マイナスになる場合は0に補正
        if ($workMinutes < 0) $workMinutes = 0;

        // hh:mm 形式に整形
        $hours = floor($workMinutes / 60);
        $minutes = $workMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }

    public function getFormattedDateAttribute()
    {
        return Carbon::parse($this->date)->format('Y年m月d日');
    }

    public function getFormattedClockInTimeAttribute()
    {
        return $this->clock_in_time
            ? Carbon::parse($this->clock_in_time)->format('H:i')
            : null;
    }

    public function getFormattedClockOutTimeAttribute()
    {
        return $this->clock_out_time
            ? Carbon::parse($this->clock_out_time)->format('H:i')
            : null;
    }

    public function getPendingClockInAttribute()
    {
        $data = is_array($this->pending_data) ? $this->pending_data : json_decode($this->pending_data, true);
        return $data['clock_in_time'] ?? null;
    }

    public function getPendingClockOutAttribute()
    {
        $data = is_array($this->pending_data) ? $this->pending_data : json_decode($this->pending_data, true);
        return $data['clock_out_time'] ?? null;
    }

    public function getPendingBreaksAttribute()
    {
        $data = is_array($this->pending_data) ? $this->pending_data : json_decode($this->pending_data, true);
        return $data['breaks'] ?? [];
    }

    public function getPendingNoteAttribute()
    {
        $data = is_array($this->pending_data) ? $this->pending_data : json_decode($this->pending_data, true);
        return $data['note'] ?? '';
    }
}
