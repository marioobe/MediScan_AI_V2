<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPredictionController extends Controller
{
    protected AiService $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $predictions = Prediction::latest()->get();
        return view('admin.predictions', compact('predictions'));
    }

    public function show(string $id)
    {
        $prediction = Prediction::findOrFail($id);
        return response()->json($prediction->toArray());
    }

    public function destroy(string $id)
    {
        $prediction = Prediction::findOrFail($id);

        if ($prediction->image_path) {
            Storage::disk('public')->delete($prediction->image_path);
        }

        $prediction->delete();

        return response()->json(['success' => true]);
    }
}
