@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-requests.css') }}">
@endsection

@section('content')
<div class="request-wrapper">
    <h2 class="page-title">申請一覧</h2>

    {{-- タブ --}}
    <div class="tab-menu">
        <a href="{{ route('admin.requests.index', ['status' => 'pending']) }}"
           class="tab {{ $status === 'pending' ? 'active' : '' }}">
           承認待ち
        </a>
        <a href="{{ route('admin.requests.index', ['status' => 'approved']) }}"
           class="tab {{ $status === 'approved' ? 'active' : '' }}">
           承認済み
        </a>
    </div>

    {{-- テーブル --}}
    <table class="request-table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
                <tr>
                    <td>
                        @switch($attendance->approval_status)
                            @case('pending') 承認待ち @break
                            @case('approved') 承認済み @break
                            @default -
                        @endswitch
                    </td>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->date?->format('Y/m/d') }}</td>
                    <td>{{ $attendance->pending_data['note'] ?? '-' }}</td>
                    <td>{{ $attendance->requested_at?->format('Y/m/d') ?? '-' }}</td>
                    <td>@if ($attendance->approval_status === 'pending')
                            <a href="{{ route('admin.stamp_correction_request.approve', $attendance->id) }}" class="detail-link">詳細</a>
                        @elseif ($attendance->approval_status === 'approved')
                            <a href="{{ route('show', $attendance->id) }}" class="detail-link">詳細</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="no-data">申請はありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection