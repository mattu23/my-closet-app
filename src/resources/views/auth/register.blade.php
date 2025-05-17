@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-tr from-blue-100 via-white to-blue-200">
    <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">新規登録</h2>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">ユーザー名</label>
                <input id="name" type="text" name="name" required autofocus class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('name') }}">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input id="email" type="email" name="email" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ old('email') }}">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input id="password" type="password" name="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">パスワード（確認）</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">新規登録</button>
        </form>
        <div class="mt-6 text-center text-sm">
            すでにアカウントをお持ちの方は
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">ログイン</a>
        </div>
    </div>
</div>
@endsection
