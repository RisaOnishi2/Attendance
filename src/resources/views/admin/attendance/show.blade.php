@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-show.css') }}">
@endsection

@section('content')
<div class="attendance-container">
    <h2>{{ $user->name }}さんの勤怠</h2>

    {{-- 月ナビゲーション --}}
    <div class="month-navigation">
        <a href="{{ route('admin.attendance.staff.show', [
            'id' => $user->id,
            'month' => $currentMonth->copy()->subMonth()->format('Y-m')
        ]) }}">← 前月</a>

        <span class="current-month">{{ $currentMonth->format('Y年m月') }}</span>

        <a href="{{ route('admin.attendance.staff.show', [
            'id' => $user->id,
            'month' => $currentMonth->copy()->addMonth()->format('Y-m')
        ]) }}">翌月 →</a>
    </div>

    <table class="attendance-table">
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dates as $date)
                @php
                    $attendance = $attendances[$date->format('Y-m-d')] ?? null;
                    $formattedDate = $date->locale('ja')->isoFormat('MM/DD(dd)');
                @endphp
                <tr>
                    <td>{{ $formattedDate }}</td>
                    <td>{{ $attendance?->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance?->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '' }}</td>
                    <td>{{ $attendance?->break_time ?? '' }}</td>
                    <td>{{ $attendance?->work_duration ?? '' }}</td>
                    <td>
                        @if($attendance)
                            <a href="{{ route('show', $attendance->id) }}">詳細</a>
                        @else
                            <a href="#">詳細</a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="csv-button">
        <button type="button">CSV出力</button>
    </div>
</div>
@endsection

