@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request.css') }}">
@endsection

@section('content')
<div class="container">
    <h2>申請一覧</h2>

    {{-- タブ --}}
    <div class="tabs">
        <a href="{{ route('request', ['status' => 'pending']) }}" 
           class="{{ $status === 'pending' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ route('request', ['status' => 'approved']) }}" 
           class="{{ $status === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    {{-- テーブル --}}
    <table class="table">
        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                <td>{{ $request->user->name }}</td>
                <td>{{ $request->target_date->format('Y/m/d') }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->created_at->format('Y/m/d') }}</td>
                <td><a href="{{ route('show', $request->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection