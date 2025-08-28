<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $clothes['name'] }}
            </h2>
            <div class="flex items-center space-x-2">
                <a href="{{ route('clothes.edit', $clothes['id']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                    編集
                </a>
                <form method="POST" action="{{ route('clothes.destroy', $clothes['id']) }}" class="inline">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- 画像セクション -->
                        <div>
                            @if($clothes['image_path'])
                                <img src="{{ asset('storage/' . $clothes['image_path']) }}" alt="{{ $clothes['name'] }}" class="w-full h-auto rounded-lg shadow-sm">
                            @else
                                <div class="w-full aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </div>

                        <!-- 詳細情報セクション -->
                        <div class="space-y-6">
                            <!-- 基本情報 -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">基本情報</h3>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">説明</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $clothes['description'] }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">カテゴリー</dt>
                                        <dd class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $clothes['category']['name'] }}
                                            </span>
                                        </dd>
                                    </div>
                                    @if($clothes['size'])
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">サイズ</dt>
                                            <dd class="mt-1">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $clothes['size'] }}
                                                </span>
                                            </dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 