@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail-container">
    <h2 class="page-title">勤怠詳細</h2>

    @if (session('success'))
        <div class="flash-message success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="flash-message error">{{ session('error') }}</div>
    @endif

    <div class="attendance-table">
        <table>
            <tr><th>名前</th><td>{{ $attendance_correct_request->user->name }}</td></tr>
            <tr><th>日付</th><td>{{ $attendance_correct_request->date->format('Y年n月j日') }}</td></tr>
            <tr><th>出勤・退勤</th>
                <td>
                    {{ $attendance_correct_request->pending_data['clock_in_time'] ?? '-' }}
                    〜
                    {{ $attendance_correct_request->pending_data['clock_out_time'] ?? '-' }}
                </td>
            </tr>
            <tr><th>休憩</th>
                <td>
                    @if(!empty($attendance_correct_request->pending_data['breaks']))
                        @foreach($attendance_correct_request->pending_data['breaks'] as $break)
                            {{ $break['start'] }} 〜 {{ $break['end'] }}<br>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr><th>備考</th><td>{{ $attendance_correct_request->pending_data['note'] ?? 'ー' }}</td></tr>
        </table>

        @if($attendance_correct_request->approval_status === 'approved')
            <div class="approved-label">
                承認済み（{{ $attendance_correct_request->approved_at ? $attendance_correct_request->approved_at->format('Y-m-d H:i') : '' }}）
            </div>
        @else
            <div class="button-area">
                <form action="{{ route('admin.stamp_correction_request.approve', $attendance_correct_request->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="approve-btn">承認</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection