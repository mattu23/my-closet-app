<?php

namespace App\Http\Controllers;

use App\Application\Services\CoordinateService;
use App\Application\Services\ClothesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CoordinateController extends Controller
{
    private CoordinateService $coordinateService;
    private ClothesService $clothesService;

    public function __construct(CoordinateService $coordinateService, ClothesService $clothesService)
    {
        $this->coordinateService = $coordinateService;
        $this->clothesService = $clothesService;
    }

    /**
     * コーディネート一覧を表示
     */
    public function index()
    {
        $userId = Auth::id();
        $coordinates = $this->coordinateService->getCoordinatesByUserId($userId);
        
        return view('coordinates.index', [
            'coordinates' => $coordinates
        ]);
    }

    /**
     * コーディネート作成フォームを表示
     */
    public function create()
    {
        $userId = Auth::id();
        $clothes = $this->clothesService->getClothesByUserId($userId);
        
        return view('coordinates.create', [
            'clothes' => $clothes
        ]);
    }

    /**
     * コーディネートを保存
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'clothes_ids' => 'required|array',
                'clothes_ids.*' => 'integer|exists:clothes,id',
                'image' => 'nullable|image|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('coordinates', 'public');
            }

            $userId = Auth::id();
            $this->coordinateService->createCoordinate(
                $validated['name'],
                $validated['description'],
                $imagePath,
                $validated['clothes_ids'],
                $userId
            );

            return redirect()->route('coordinates.index')
                ->with('success', 'コーディネートを作成しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'コーディネートの作成に失敗しました。')
                ->withInput();
        }
    }

    /**
     * コーディネート詳細を表示
     */
    public function show($id)
    {
        $userId = Auth::id();
        $coordinates = $this->coordinateService->getCoordinatesByUserId($userId);
        
        // 表示対象のコーディネートを取得
        $coordinate = null;
        foreach ($coordinates as $item) {
            if ($item->getId() == $id) {
                $coordinate = $item;
                break;
            }
        }

        if (!$coordinate) {
            return redirect()->route('coordinates.index')
                ->with('error', 'コーディネートが見つかりません。');
        }

        // コーディネートに含まれる洋服を取得
        $clothes = [];
        $allClothes = $this->clothesService->getClothesByUserId($userId);
        foreach ($allClothes as $item) {
            if (in_array($item->getId(), $coordinate->getClothesIds())) {
                $clothes[] = $item;
            }
        }

        return view('coordinates.show', [
            'coordinate' => $coordinate,
            'clothes' => $clothes
        ]);
    }

    /**
     * コーディネート編集フォームを表示
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $coordinates = $this->coordinateService->getCoordinatesByUserId($userId);
        $allClothes = $this->clothesService->getClothesByUserId($userId);
        
        // 編集対象のコーディネートを取得
        $coordinate = null;
        foreach ($coordinates as $item) {
            if ($item->getId() == $id) {
                $coordinate = $item;
                break;
            }
        }

        if (!$coordinate) {
            return redirect()->route('coordinates.index')
                ->with('error', 'コーディネートが見つかりません。');
        }

        return view('coordinates.edit', [
            'coordinate' => $coordinate,
            'allClothes' => $allClothes
        ]);
    }

    /**
     * コーディネートを更新
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'clothes_ids' => 'required|array',
                'clothes_ids.*' => 'integer|exists:clothes,id',
                'image' => 'nullable|image|max:2048'
            ]);

            // 現在のコーディネート情報を取得
            $userId = Auth::id();
            $coordinates = $this->coordinateService->getCoordinatesByUserId($userId);
            
            $coordinate = null;
            foreach ($coordinates as $item) {
                if ($item->getId() == $id) {
                    $coordinate = $item;
                    break;
                }
            }

            if (!$coordinate) {
                return redirect()->route('coordinates.index')
                    ->with('error', 'コーディネートが見つかりません。');
            }

            $imagePath = $coordinate->getImagePath();
            if ($request->hasFile('image')) {
                // 古い画像を削除
                if ($imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('coordinates', 'public');
            }

            $this->coordinateService->updateCoordinate(
                $id,
                $validated['name'],
                $validated['description'],
                $imagePath,
                $validated['clothes_ids']
            );

            return redirect()->route('coordinates.index')
                ->with('success', 'コーディネートを更新しました。');
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'コーディネートの更新に失敗しました。')
                ->withInput();
        }
    }

    /**
     * コーディネートを削除
     */
    public function destroy($id)
    {
        try {
            $result = $this->coordinateService->deleteCoordinate($id);

            if (!$result) {
                return redirect()->back()
                    ->with('error', 'コーディネートの削除に失敗しました。');
            }

            return redirect()->route('coordinates.index')
                ->with('success', 'コーディネートを削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'コーディネートの削除に失敗しました。');
        }
    }

    /**
     * コーディネートに洋服を追加
     */
    public function addClothes($coordinateId, $clothesId)
    {
        try {
            $result = $this->coordinateService->addClothesToCoordinate($coordinateId, $clothesId);

            if (!$result) {
                return redirect()->back()
                    ->with('error', '洋服の追加に失敗しました。');
            }

            return redirect()->route('coordinates.show', $coordinateId)
                ->with('success', '洋服を追加しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の追加に失敗しました。');
        }
    }

    /**
     * コーディネートから洋服を削除
     */
    public function removeClothes($coordinateId, $clothesId)
    {
        try {
            $result = $this->coordinateService->removeClothesFromCoordinate($coordinateId, $clothesId);

            if (!$result) {
                return redirect()->back()
                    ->with('error', '洋服の削除に失敗しました。');
            }

            return redirect()->route('coordinates.show', $coordinateId)
                ->with('success', '洋服を削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の削除に失敗しました。');
        }
    }
} 