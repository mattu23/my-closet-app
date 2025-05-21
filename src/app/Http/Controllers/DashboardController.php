<?php

namespace App\Http\Controllers;

use App\Application\Services\ClothesService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private ClothesService $clothesService;

    public function __construct(ClothesService $clothesService,)
    {
        $this->clothesService = $clothesService;
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