<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('洋服を編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('clothes.update', $clothes->getId()) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('名前')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $clothes->getName())" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('説明')" />
                            <textarea
                                id="description"
                                name="description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                required
                            >{{ old('description', $clothes->getDescription()) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div>
                            <x-input-label for="category_id" :value="__('カテゴリー')" />
                            <select id="category_id" name="category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">選択してください</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->getId() }}" {{ old('category_id', $clothes->getCategory()->getId()) == $category->getId() ? 'selected' : '' }}>
                                        {{ $category->getName() }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div>
                            <x-input-label for="size" :value="__('サイズ')" />
                            <select id="size" name="size" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">選択してください</option>
                                @foreach($availableSizes as $size)
                                    <option value="{{ $size }}" {{ old('size', $clothes->getSize()) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('size')" />
                        </div>

                        <div>
                            <x-input-label for="color_name" :value="__('色の名前')" />
                            <x-text-input id="color_name" name="color_name" type="text" class="mt-1 block w-full" :value="old('color_name', $clothes->getColor()?->getName())" />
                            <x-input-error class="mt-2" :messages="$errors->get('color_name')" />
                        </div>

                        <div>
                            <x-input-label for="color_code" :value="__('色のコード')" />
                            <x-text-input id="color_code" name="color_code" type="color" class="mt-1 block w-full h-10" :value="old('color_code', $clothes->getColor()?->getHexCode() ?? '#000000')" />
                            <x-input-error class="mt-2" :messages="$errors->get('color_code')" />
                        </div>

                        <div>
                            <x-input-label for="brand_name" :value="__('ブランド名')" />
                            <x-text-input id="brand_name" name="brand_name" type="text" class="mt-1 block w-full" :value="old('brand_name', $clothes->getBrand()?->getName())" />
                            <x-input-error class="mt-2" :messages="$errors->get('brand_name')" />
                        </div>

                        <div>
                            <x-input-label for="brand_description" :value="__('ブランドの説明')" />
                            <textarea
                                id="brand_description"
                                name="brand_description"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="2"
                            >{{ old('brand_description', $clothes->getBrand()?->getDescription()) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('brand_description')" />
                        </div>

                        <div>
                            <x-input-label for="brand_country" :value="__('ブランドの国')" />
                            <x-text-input id="brand_country" name="brand_country" type="text" class="mt-1 block w-full" :value="old('brand_country', $clothes->getBrand()?->getCountry())" />
                            <x-input-error class="mt-2" :messages="$errors->get('brand_country')" />
                        </div>

                        <div>
                            <x-input-label for="image" :value="__('画像')" />
                            @if($clothes->getImagePath())
                                <div class="mt-2 mb-4">
                                    <img src="{{ asset('storage/' . $clothes->getImagePath()) }}" alt="{{ $clothes->getName() }}" class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            @endif
                            <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('更新') }}</x-primary-button>
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                {{ __('キャンセル') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 