<?php

namespace App\Http\Controllers;

use App\Application\Services\ClothesService;
use App\Application\Services\CategoryService;
use Illuminate\View\View;

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
        $dashboardData = $this->clothesService->getDashboardData();
        return view('dashboard', $dashboardData);
    }
} 