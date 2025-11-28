@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/stamp_correction_request.css') }}">
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">
    <h2 class="text-2xl font-bold mb-6">申請一覧</h2>

    {{-- タブメニュー --}}
    <div class="flex border-b border-gray-300 mb-4">
        <a href="{{ route('stamp_correction_request.list', ['tab' => 'pending']) }}"
           class="tab-button {{ $tab === 'pending' ? 'active' : '' }}">
           承認待ち
        </a>
        <a href="{{ route('stamp_correction_request.list', ['tab' => 'approved']) }}"
           class="tab-button {{ $tab === 'approved' ? 'active' : '' }}">
           承認済み
        </a>
    </div>

    {{-- 承認待ち --}}
    @if ($tab === 'pending')
    <div class="table-wrapper">
        <table>
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
                @forelse ($pending as $item)
                <tr>
                    <td class="status-pending">承認待ち</td>
                    <td>{{ $item->user->name }}</td>
                    <td><strong>{{ $item->date->format('Y/m/d') }}</strong></td>
                    <td>{{ $item->pending_data['note'] ?? '—' }}</td>
                    <td>{{ optional($item->requested_at)->format('Y/m/d') ?? '—' }}</td>
                    <td><a href="{{ route('show', ['id' => $item->id]) }}">詳細</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">承認待ちの申請はありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif

    {{-- 承認済み --}}
    @if ($tab === 'approved')
    <div class="table-wrapper">
        <table>
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
                @forelse ($approved as $item)
                <tr>
                    <td class="status-approved">承認済み</td>
                    <td>{{ $item->user->name }}</td>
                    <td><strong>{{ $item->date->format('Y/m/d') }}</strong></td>
                    <td>{{ $item->pending_data['note'] ?? '—' }}</td>
                    <td>{{ optional($item->requested_at)->format('Y/m/d') ?? '—' }}</td>
                    <td><a href="#">詳細</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4">承認済みの申請はありません。</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection