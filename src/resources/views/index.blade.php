@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <div class="attendance-status">
        @if($attendance->work_status === 'off')
            勤務外
        @elseif($attendance->work_status === 'working')
            出勤中
        @elseif($attendance->work_status === 'break')
            休憩中
        @elseif($attendance->work_status === 'finished')
            退勤済
        @else
            不明
        @endif
    </div>

    <div class="attendance-date">
        {{ $attendance->formatted_date }}
    </div>

    <div class="attendance-time">
        {{ $attendance->formatted_clock_in_time }}
    </div>

    {{-- ボタン制御 --}}
    @if($attendance->work_status === 'off')
        <form action="{{ route('clockIn') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button">出勤</button>
        </form>
    @elseif($attendance->work_status === 'working')
        <form action="{{ route('startBreak') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="attendance-button">休憩入</button>
        </form>
        <form action="{{ route('clockOut') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="attendance-button">退勤</button>
        </form>
    @elseif($attendance->work_status === 'break')
        <form action="{{ route('endBreak') }}" method="POST">
            @csrf
            <button type="submit" class="attendance-button">休憩戻</button>
        </form>
    @endif
</div>
@endsection