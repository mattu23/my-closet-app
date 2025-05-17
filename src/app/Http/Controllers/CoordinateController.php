<?php

namespace App\Http\Controllers;

use App\Application\Services\CoordinateService;
use App\Application\Services\ClothesService;
use Illuminate\Http\Request;
use Illuminate\View\View;
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
    public function index(): View
    {
        $coordinatesData = $this->coordinateService->getCoordinatesData();
        return view('coordinates.index', $coordinatesData);
    }

    /**
     * コーディネート作成フォームを表示
     */
    public function create(): View
    {
        $formData = $this->coordinateService->getCreateFormData();
        return view('coordinates.create', $formData);
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

            $this->coordinateService->createCoordinate($validated, $request->file('image'));

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
    public function show(int $id): View
    {
        $coordinateData = $this->coordinateService->getCoordinateDetail($id);
        return view('coordinates.show', $coordinateData);
    }

    /**
     * コーディネート編集フォームを表示
     */
    public function edit(int $id): View
    {
        $editData = $this->coordinateService->getEditFormData($id);
        return view('coordinates.edit', $editData);
    }

    /**
     * コーディネートを更新
     */
    public function update(Request $request, int $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'clothes_ids' => 'required|array',
                'clothes_ids.*' => 'integer|exists:clothes,id',
                'image' => 'nullable|image|max:2048'
            ]);

            $this->coordinateService->updateCoordinate($id, $validated, $request->file('image'));

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
    public function destroy(int $id)
    {
        try {
            $this->coordinateService->deleteCoordinate($id);
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
    public function addClothes(int $coordinateId, int $clothesId)
    {
        try {
            $this->coordinateService->addClothesToCoordinate($coordinateId, $clothesId);
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
    public function removeClothes(int $coordinateId, int $clothesId)
    {
        try {
            $this->coordinateService->removeClothesFromCoordinate($coordinateId, $clothesId);
            return redirect()->route('coordinates.show', $coordinateId)
                ->with('success', '洋服を削除しました。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '洋服の削除に失敗しました。');
        }
    }
} 