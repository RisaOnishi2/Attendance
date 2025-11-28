@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin-attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h1 class="attendance-title">
        {{ $targetDate->format('Y年n月j日') }}の勤怠
    </h1>

    <div class="attendance-nav">
        <a href="{{ route('admin.attendance.index', ['date' => $targetDate->copy()->subDay()->toDateString()]) }}">← 前日</a>

        <div class="attendance-date-picker">
            <i class="fa-solid fa-calendar"></i>
            <input type="date" id="datePicker" value="{{ $targetDate->toDateString() }}" 
                   onchange="location.href='{{ route('admin.attendance.index') }}?date=' + this.value">
        </div>

        <a href="{{ route('admin.attendance.index', ['date' => $targetDate->copy()->addDay()->toDateString()]) }}">翌日 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>名前</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->clock_in_time ? $attendance->clock_in_time->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->clock_out_time ? $attendance->clock_out_time->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->break_time ?? '-' }}</td>
                    <td>{{ $attendance->work_duration ?? '-' }}</td>
                    <td>
                        <a href="{{ route('show', $attendance->id) }}">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="attendance-empty">この日の勤怠データはありません。</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
