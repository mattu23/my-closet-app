<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                カテゴリー一覧
            </h2>
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                カテゴリーを追加
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($categories->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">カテゴリーが登録されていません。</p>
                            <a href="{{ route('categories.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                カテゴリーを追加する
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($categories as $category)
                                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                    <div class="p-6">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900">{{ $category->getName() }}</h3>
                                                @if($category->getDescription())
                                                    <p class="mt-1 text-sm text-gray-500">{{ $category->getDescription() }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('categories.edit', $category->getId()) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                    編集
                                                </a>
                                                <form method="POST" action="{{ route('categories.destroy', $category->getId()) }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('本当に削除しますか？')">
                                                        削除
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        @if($category->getChildren()->isNotEmpty())
                                            <div class="mt-4 pl-4 border-l-2 border-gray-200">
                                                <h4 class="text-sm font-medium text-gray-700 mb-2">サブカテゴリー</h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    @foreach($category->getChildren() as $child)
                                                        <div class="bg-gray-50 rounded-lg p-4">
                                                            <div class="flex justify-between items-start">
                                                                <div>
                                                                    <h5 class="text-sm font-medium text-gray-900">{{ $child->getName() }}</h5>
                                                                    @if($child->getDescription())
                                                                        <p class="mt-1 text-xs text-gray-500">{{ $child->getDescription() }}</p>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center space-x-2">
                                                                    <a href="{{ route('categories.edit', $child->getId()) }}" class="inline-flex items-center px-2 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                                                        編集
                                                                    </a>
                                                                    <form method="POST" action="{{ route('categories.destroy', $child->getId()) }}" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700" onclick="return confirm('本当に削除しますか？')">
                                                                            削除
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 