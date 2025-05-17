<?php

namespace App\Http\Controllers;

use App\Application\Services\ClothesService;
use App\Application\Services\CategoryService;
use App\Domain\ValueObjects\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ClothesController extends Controller
{
    private ClothesService $clothesService;
    private CategoryService $categoryService;

    public function __construct(ClothesService $clothesService, CategoryService $categoryService)
    {
        $this->clothesService = $clothesService;
        $this->categoryService = $categoryService;
    }

    /**
     * 洋服一覧を表示
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $clothes = $this->clothesService->getClothesByUserId($userId);
        
        // フィルタリング
        $size = $request->query('size');
        $brand = $request->query('brand');
        $color = $request->query('color');
        
        if ($size) {
            try {
                $clothes = $this->clothesService->getClothesBySize($userId, $size);
            } catch (\InvalidArgumentException $e) {
                // 無効なサイズの場合は無視
            }
        }
        
        if ($brand) {
            $clothes = $this->clothesService->getClothesByBrand($userId, $brand);
        }
        
        if ($color === 'dark') {
            $clothes = $this->clothesService->getDarkColoredClothes($userId);
        } elseif ($color === 'bright') {
            $clothes = $this->clothesService->getBrightColoredClothes($userId);
        }
        
        // 利用可能なサイズのリスト
        $availableSizes = Size::getAvailableSizes();
        
        return view('clothes.index', [
            'clothes' => $clothes,
            'availableSizes' => $availableSizes
        ]);
    }

    /**
     * 洋服作成フォームを表示
     */
    public function create()
    {
        $userId = Auth::id();
        $categories = $this->categoryService->getRootCategories();
        $availableSizes = Size::getAvailableSizes();
        
        return view('clothes.create', [
            'categories' => $categories,
            'availableSizes' => $availableSizes
        ]);
    }

    /**
     * 洋服を保存
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id',
                'size' => 'nullable|string|max:10',
                'color_name' => 'nullable|string|max:50',
                'color_code' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'brand_name' => 'nullable|string|max:100',
                'brand_description' => 'nullable|string',
                'brand_country' => 'nullable|string|max:50',
                'image' => 'nullable|image|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('clothes', 'public');
            }

            // 色情報の準備
            $colorData = null;
            if ($validated['color_name'] && $validated['color_code']) {
                $colorData = [
                    'name' => $validated['color_name'],
                    'hex_code' => $validated['color_code']
                ];
            }
            
            // ブランド情報の準備
            $brandData = null;
            if ($validated['brand_name']) {
                $brandData = [
                    'name' => $validated['brand_name'],
                    'description' => $validated['brand_description'],
                    'country' => $validated['brand_country']
                ];
            }

            $userId = Auth::id();
            $this->clothesService->createClothes(
                $validated['name'],
                $validated['description'],
                $imagePath,
                $validated['category_id'],
                $userId,
                $validated['size'],
                $colorData,
                $brandData
            );

            return redirect()->route('clothes.index')
                ->with('success', '洋服を登録しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の登録に失敗しました。' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 洋服詳細を表示
     */
    public function show($id)
    {
        $userId = Auth::id();
        $clothes = $this->clothesService->getClothesByUserId($userId);
        
        // 表示対象の洋服を取得
        $targetClothes = null;
        foreach ($clothes as $item) {
            if ($item->getId() == $id) {
                $targetClothes = $item;
                break;
            }
        }

        if (!$targetClothes) {
            return redirect()->route('clothes.index')
                ->with('error', '洋服が見つかりません。');
        }

        return view('clothes.show', [
            'clothes' => $targetClothes
        ]);
    }

    /**
     * 洋服編集フォームを表示
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $clothes = $this->clothesService->getClothesByUserId($userId);
        $categories = $this->categoryService->getRootCategories();
        $availableSizes = Size::getAvailableSizes();
        
        // 編集対象の洋服を取得
        $targetClothes = null;
        foreach ($clothes as $item) {
            if ($item->getId() == $id) {
                $targetClothes = $item;
                break;
            }
        }

        if (!$targetClothes) {
            return redirect()->route('clothes.index')
                ->with('error', '洋服が見つかりません。');
        }

        return view('clothes.edit', [
            'clothes' => $targetClothes,
            'categories' => $categories,
            'availableSizes' => $availableSizes
        ]);
    }

    /**
     * 洋服を更新
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'category_id' => 'required|integer|exists:categories,id',
                'size' => 'nullable|string|max:10',
                'color_name' => 'nullable|string|max:50',
                'color_code' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'brand_name' => 'nullable|string|max:100',
                'brand_description' => 'nullable|string',
                'brand_country' => 'nullable|string|max:50',
                'image' => 'nullable|image|max:2048'
            ]);

            // 現在の洋服情報を取得
            $userId = Auth::id();
            $clothes = $this->clothesService->getClothesByUserId($userId);
            
            $targetClothes = null;
            foreach ($clothes as $item) {
                if ($item->getId() == $id) {
                    $targetClothes = $item;
                    break;
                }
            }

            if (!$targetClothes) {
                return redirect()->route('clothes.index')
                    ->with('error', '洋服が見つかりません。');
            }

            $imagePath = $targetClothes->getImagePath();
            if ($request->hasFile('image')) {
                // 古い画像を削除
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('clothes', 'public');
            }

            // 色情報の準備
            $colorData = null;
            if ($validated['color_name'] && $validated['color_code']) {
                $colorData = [
                    'name' => $validated['color_name'],
                    'hex_code' => $validated['color_code']
                ];
            }
            
            // ブランド情報の準備
            $brandData = null;
            if ($validated['brand_name']) {
                $brandData = [
                    'name' => $validated['brand_name'],
                    'description' => $validated['brand_description'],
                    'country' => $validated['brand_country']
                ];
            }

            $this->clothesService->updateClothes(
                $id,
                $validated['name'],
                $validated['description'],
                $imagePath,
                $validated['category_id'],
                $validated['size'],
                $colorData,
                $brandData
            );

            return redirect()->route('clothes.index')
                ->with('success', '洋服を更新しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の更新に失敗しました。' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * 洋服を削除
     */
    public function destroy($id)
    {
        try {
            $result = $this->clothesService->deleteClothes($id);

            if (!$result) {
                return redirect()->back()
                    ->with('error', '洋服の削除に失敗しました。');
            }

            return redirect()->route('clothes.index')
                ->with('success', '洋服を削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の削除に失敗しました。');
        }
    }
} 