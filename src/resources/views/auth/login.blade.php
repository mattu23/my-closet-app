@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-tr from-blue-100 via-white to-blue-200">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">ログイン</h2>
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" type="email" name="email" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('email') }}">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">ログイン状態を保持</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        パスワードをお忘れですか？
                    </a>
                @endif
            </div>
            <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">ログイン</button>
        </form>
        <div class="mt-6 text-center text-sm">
            アカウントをお持ちでない方は
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline">新規登録</a>
        </div>
    </div>
</div>
@endsection
