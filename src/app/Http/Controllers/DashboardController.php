<?php

namespace App\Http\Controllers;

use App\Application\Services\ClothesService;
use App\Application\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    private ClothesService $clothesService;
    private CategoryService $categoryService;

    public function __construct(ClothesService $clothesService, CategoryService $categoryService)
    {
        $this->clothesService = $clothesService;
        $this->categoryService = $categoryService;
    }

    /**
     * ダッシュボードを表示
     */
    public function index(): View
    {
        $userId = Auth::id();
        $clothes = collect($this->clothesService->getClothesByUserId($userId));
        $categories = $this->categoryService->getRootCategories();

        return view('dashboard', [
            'clothes' => $clothes,
            'categories' => $categories
        ]);
    }
} 