<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Attendance Management</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/login">
                    <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
                </a>
                <nav class="nav">
                    @auth
                        @if (Auth::user()->is_admin)
                            {{-- 管理者向けメニュー --}}
                                <a href="/admin/attendance/list">勤怠一覧</a>
                                <a href="/admin/staff/list">スタッフ一覧</a>
                                <a href="{{ route('admin.requests.index') }}">申請一覧</a>
                                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="logout-button">ログアウト</button>
                                </form>
                        @else
                            {{-- 一般ユーザー向けメニュー --}}
                                <a href="{{ route('index') }}">勤怠</a>
                                <a href="{{ route('list') }}">勤怠一覧</a>
                                <a href="/stamp_correction_request/list">申請</a>
                                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="logout-button">ログアウト</button>
                                </form>
                        @endif
                    @endauth
                </nav>  
            </div>
        </div>
    </header>

  <main>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @yield('content')
  </main>
</body>

</html>
