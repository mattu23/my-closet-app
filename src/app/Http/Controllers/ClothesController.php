<?php

namespace App\Http\Controllers;

use App\Application\Services\ClothesService;
use App\Application\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'size' => 'nullable|string|max:10',
            'brand' => 'nullable|string|max:100',
            'color' => 'nullable|string|in:dark,bright'
        ]);

        $clothesData = $this->clothesService->getFilteredClothes($validated);
        
        return view('clothes.index', $clothesData);
    }

    /**
     * 洋服作成フォームを表示
     */
    public function create(): View
    {
        $formData = $this->clothesService->getCreateFormData();
        return view('clothes.create', $formData);
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

            $this->clothesService->createClothes($validated, $request->file('image'));

            return redirect()->route('clothes.index')
                ->with('success', '洋服を登録しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の登録に失敗しました。')
                ->withInput();
        }
    }

    /**
     * 洋服詳細を表示
     */
    public function show(int $id): View
    {
        $clothesData = $this->clothesService->getClothesDetail($id);
        return view('clothes.show', $clothesData);
    }

    /**
     * 洋服編集フォームを表示
     */
    public function edit(int $id): View
    {
        $editData = $this->clothesService->getEditFormData($id);
        return view('clothes.edit', $editData);
    }

    /**
     * 洋服を更新
     */
    public function update(Request $request, int $id)
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

            $this->clothesService->updateClothes($id, $validated, $request->file('image'));

            return redirect()->route('clothes.index')
                ->with('success', '洋服を更新しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の更新に失敗しました。')
                ->withInput();
        }
    }

    /**
     * 洋服を削除
     */
    public function destroy(int $id)
    {
        try {
            $this->clothesService->deleteClothes($id);
            return redirect()->route('clothes.index')
                ->with('success', '洋服を削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の削除に失敗しました。');
        }
    }
} 