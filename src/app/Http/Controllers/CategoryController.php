<?php

namespace App\Http\Controllers;

use App\Application\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * カテゴリー一覧を表示
     */
    public function index(): View
    {
        $categories = $this->categoryService->getRootCategories();
        return view('categories.index', [
            'categories' => $categories
        ]);
    }

    /**
     * カテゴリー作成フォームを表示
     */
    public function create(): View
    {
        $categories = $this->categoryService->getRootCategories();
        return view('categories.create', [
            'categories' => $categories
        ]);
    }

    /**
     * カテゴリーを保存
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:categories,id'
        ]);

        $category = $this->categoryService->createCategory($validated);

        return redirect()->route('categories.index')
            ->with('success', 'カテゴリーを作成しました。');
    }

    /**
     * カテゴリー詳細を表示
     */
    public function show(int $id): View
    {
        $category = $this->categoryService->getCategory($id);
        if (!$category) {
            abort(404);
        }
        return view('categories.show', [
            'category' => $category
        ]);
    }

    /**
     * カテゴリー編集フォームを表示
     */
    public function edit(int $id): View
    {
        $category = $this->categoryService->getCategory($id);
        if (!$category) {
            abort(404);
        }
        $categories = $this->categoryService->getRootCategories();
        return view('categories.edit', [
            'category' => $category,
            'categories' => $categories
        ]);
    }

    /**
     * カテゴリーを更新
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:categories,id'
        ]);

        $category = $this->categoryService->updateCategory($id, $validated);
        if (!$category) {
            abort(404);
        }

        return redirect()->route('categories.index')
            ->with('success', 'カテゴリーを更新しました。');
    }

    /**
     * カテゴリーを削除
     */
    public function destroy(int $id)
    {
        $this->categoryService->deleteCategory($id);
        return redirect()->route('categories.index')
            ->with('success', 'カテゴリーを削除しました。');
    }

    /**
     * 子カテゴリーを取得
     */
    public function children(int $id)
    {
        $categories = $this->categoryService->getChildCategories($id);
        return view('categories.children', [
            'categories' => $categories
        ]);
    }
} 