<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Closet App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-tr from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-sm mx-auto bg-white rounded-2xl shadow-2xl p-8 flex flex-col items-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 tracking-tight">My Closet App</h1>
        <p class="mb-6 text-gray-500 text-center leading-relaxed">あなたの洋服をスマートに管理できる<br>モダンなクローゼットアプリ</p>
        <div class="flex flex-col gap-3 w-full">
            <a href="{{ route('login') }}" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition text-center">ログイン</a>
            <a href="{{ route('register') }}" class="w-full py-2 bg-white border border-blue-600 text-blue-600 rounded-lg font-semibold shadow hover:bg-blue-50 transition text-center">新規登録</a>
        </div>
    </div>
    <footer class="mt-8 text-gray-400 text-xs text-center w-full">
        &copy; {{ date('Y') }} My Closet App
    </footer>
</body>
</html> 