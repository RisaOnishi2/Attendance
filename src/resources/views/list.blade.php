@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="attendance-list">
    <h2>勤怠一覧</h2>

    <div class="month-nav">
        <a href="{{ route('list', [$current->copy()->subMonth()->year, $current->copy()->subMonth()->month]) }}">← 前月</a>
        <span>{{ $current->format('Y/m') }}</span>
        <a href="{{ route('list', [$current->copy()->addMonth()->year, $current->copy()->addMonth()->month]) }}">翌月 →</a>
    </div>

    <table>
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
            @php
                $weekMap = ['日', '月', '火', '水', '木', '金', '土'];
            @endphp

            @for ($day = $current->copy()->startOfMonth(); $day->lte($current->copy()->endOfMonth()); $day->addDay())
                @php
                    $record = $attendances[$day->format('Y-m-d')] ?? null;
                    $youbi = $weekMap[$day->dayOfWeek];
                @endphp
                <tr>
                    <td>{{ $day->format('m/d') }}({{ $youbi }})</td>
                    <td>{{ $record && $record->clock_in_time ? \Carbon\Carbon::parse($record->clock_in_time)->format('H:i') : '' }}</td>
                    <td>{{ $record && $record->clock_out_time ? \Carbon\Carbon::parse($record->clock_out_time)->format('H:i') : '' }}</td>

                    <td>{{ $record && $record->break_time ? \Carbon\Carbon::parse($record->break_time)->format('H:i') : '00:00' }}</td>
                    <td>{{ $record && $record->total_time ? \Carbon\Carbon::parse($record->total_time)->format('H:i') : '00:00' }}</td>

                    <td>
                        @if($record)
                            <a href="{{ route('show', ['id' => $record->id]) }}">詳細</a>
                        @endif
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@endsection