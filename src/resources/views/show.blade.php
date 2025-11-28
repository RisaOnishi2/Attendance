@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h2 class="title">勤怠詳細</h2>

    @if(session('success'))
        <p class="alert-success">{{ session('success') }}</p>
    @endif

    @if($attendance->approval_status === 'pending')
        <p class="text-red-500">この勤怠は承認待ちです。修正はできません。</p>
        <div class="detail-box">
            <table class="detail-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        {{ $attendance->pending_clock_in ? \Carbon\Carbon::parse($attendance->pending_clock_in)->timezone('Asia/Tokyo')->format('H:i') : '-' }}
                            〜
                        {{ $attendance->pending_clock_out ? \Carbon\Carbon::parse($attendance->pending_clock_out)->timezone('Asia/Tokyo')->format('H:i') : '-' }}
                    </td>
                </tr>
                @foreach($breaks as $index => $break)
                <tr>
                    <th>休憩{{ $index + 1 }}</th>
                    <td>
                        {{ $break->start_time ? \Carbon\Carbon::parse($break->start_time)->format('H:i') : '-' }}
                        〜
                        {{ $break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '-' }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th>備考</th>
                    <td>{{ $attendance->pending_note ?? '-' }}</td>
                </tr>
            </table>
        </div>  
    @else
        <form method="POST" action="{{ route('update', $attendance->id) }}">
            @csrf
            @method('PUT')
            <div class="detail-box">
                <table class="detail-table">
                        <tr>
                            <th>名前</th>
                            <td>{{ $attendance->user->name }}</td>
                        </tr>
                        <tr>
                            <th>日付</th>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('Y年n月j日') }}</td>
                        </tr>
                        <tr>
                            <th>出勤・退勤</th>
                            <td>
                                <input type="time" name="clock_in_time" value="{{ old('clock_in_time', $attendance->clock_in_time ? \Carbon\Carbon::parse($attendance->clock_in_time)->format('H:i') : '') }}">
                                〜
                                <input type="time" name="clock_out_time" value="{{ old('clock_out_time', $attendance->clock_out_time ? \Carbon\Carbon::parse($attendance->clock_out_time)->format('H:i') : '') }}">

                                @error('clock_in_time')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                                @error('clock_out_time')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>

                        {{-- 休憩時間 --}}
                        @foreach($breaks as $index => $break)
                            <tr>
                                <th>休憩{{ $index + 1 }}</th>
                                <td>
                                    <input type="time" name="breaks[{{ $index }}][start]" value="{{ old("breaks.$index.start", $break->start_time ? \Carbon\Carbon::parse($break->start_time)->format('H:i') : '') }}">
                                    〜
                                    <input type="time" name="breaks[{{ $index }}][end]" value="{{ old("breaks.$index.end", $break->end_time ? \Carbon\Carbon::parse($break->end_time)->format('H:i') : '') }}">

                                    @error("breaks.$index.start")
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                    @error("breaks.$index.end")
                                        <p class="text-red-500">{{ $message }}</p>
                                    @enderror
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <th>備考</th>
                            <td>
                                <textarea name="note">{{ old('note', $attendance->note ?? '') }}</textarea>
                                @error('note')
                                    <p class="text-red-500">{{ $message }}</p>
                                @enderror
                            </td>
                        </tr>
                </table>
            </div>   
            <div class="button-area">
                <button type="submit" class="btn">修正</button>
            </div>
        </form>
    @endif
</div>
@endsection