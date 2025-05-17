<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                マイクローゼット
            </h2>
            <a href="{{ route('clothes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                洋服を追加
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- フィルターセクション -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('clothes.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- サイズフィルター -->
                            <div>
                                <label for="size" class="block text-sm font-medium text-gray-700">サイズ</label>
                                <select name="size" id="size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">すべて</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size }}" {{ request('size') == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ブランドフィルター -->
                            <div>
                                <label for="brand" class="block text-sm font-medium text-gray-700">ブランド</label>
                                <select name="brand" id="brand" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">すべて</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->getId() }}" {{ request('brand') == $brand->getId() ? 'selected' : '' }}>
                                            {{ $brand->getName() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 色フィルター -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700">色</label>
                                <select name="color" id="color" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">すべて</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->getId() }}" {{ request('color') == $color->getId() ? 'selected' : '' }}>
                                            {{ $color->getName() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- カテゴリーフィルター -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">カテゴリー</label>
                                <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">すべて</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->getId() }}" {{ request('category') == $category->getId() ? 'selected' : '' }}>
                                            {{ $category->getName() }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                フィルターを適用
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 洋服一覧 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
                                            <h3 class="text-lg font-medium text-gray-900">{{ $item->getName() }}</h3>
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