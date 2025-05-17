<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $category->getName() }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('categories.edit', $category->getId()) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    編集
                </a>
                <form method="POST" action="{{ route('categories.destroy', $category->getId()) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('本当に削除しますか？')">
                        削除
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- カテゴリー情報 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">説明</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $category->getDescription() ?? '説明はありません。' }}</p>
                        </div>

                        @if($category->getParent())
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">親カテゴリー</h3>
                                <a href="{{ route('categories.show', $category->getParent()->getId()) }}" class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200">
                                    {{ $category->getParent()->getName() }}
                                </a>
                            </div>
                        @endif

                        @if($category->getChildren()->isNotEmpty())
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">サブカテゴリー</h3>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($category->getChildren() as $child)
                                        <a href="{{ route('categories.show', $child->getId()) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200">
                                            {{ $child->getName() }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 洋服一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">このカテゴリーの洋服</h3>
                        <a href="{{ route('clothes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            洋服を追加
                        </a>
                    </div>

                    @if($clothes->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">このカテゴリーに属する洋服はありません。</p>
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