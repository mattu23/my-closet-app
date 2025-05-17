<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- ユーザー情報セクション -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-16 h-16 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 洋服一覧セクション -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">マイクローゼット</h3>
                        <a href="{{ route('clothes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            洋服を追加
                        </a>
                    </div>

                    @if($clothes->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">洋服が登録されていません。</p>
                            <a href="{{ route('clothes.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                洋服を追加する
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($clothes as $item)
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                    <a href="{{ route('clothes.show', $item->getId()) }}" class="block">
                                        @if($item->getImagePath())
                                            <img src="{{ asset('storage/' . $item->getImagePath()) }}" alt="{{ $item->getName() }}" class="w-full h-48 object-cover">
                                        @else
                                            <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
                                                <span class="text-gray-400">No Image</span>
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            <h4 class="text-lg font-medium text-gray-900">{{ $item->getName() }}</h4>
                                            <p class="mt-1 text-sm text-gray-500">{{ Str::limit($item->getDescription(), 100) }}</p>
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @if($item->getSize())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $item->getSize() }}
                                                    </span>
                                                @endif
                                                @if($item->getColor())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $item->getColor()->getName() }}
                                                    </span>
                                                @endif
                                                @if($item->getCategory())
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $item->getCategory()->getName() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
