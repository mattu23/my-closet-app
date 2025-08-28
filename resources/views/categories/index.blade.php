                    @if($categories->isEmpty())
                        <div class="text-center py-12">
                            <p class="text-gray-500">カテゴリーが登録されていません。</p>
                            <a href="{{ route('categories.create') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                カテゴリーを追加する
                            </a>
                        </div>
                    @else
                        <div class="space-y-6"> 