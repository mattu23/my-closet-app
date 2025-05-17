<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <a href="{{ route('dashboard') }}" class="text-lg font-bold text-gray-800 flex items-center">
                    <x-application-logo class="block h-8 w-auto mr-2" />
                    Dashboard
                </a>
                <!-- タブ型ナビゲーション -->
                <div class="flex space-x-4 ml-8">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        ダッシュボード
                    </a>
                    <a href="{{ route('clothes.create') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('clothes.create') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        洋服追加
                    </a>
                    <a href="{{ route('categories.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('categories.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        カテゴリー管理
                    </a>
                    <a href="{{ route('profile.edit') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        ユーザー情報
                    </a>
                </div>
            </div>
            <div>
                <!-- ログアウトボタン -->
                @if (Auth::check())
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">ログアウト</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                ダッシュボード
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('clothes.create')" :active="request()->routeIs('clothes.create')">
                洋服追加
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
                カテゴリー管理
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                ユーザー情報
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        ログアウト
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
