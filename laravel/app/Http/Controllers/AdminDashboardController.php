<?php

namespace App\Http\Controllers;

use App\Models\AiModel;
use App\Models\Prediction;
use App\Models\TrainingJob;
use App\Services\AiService;

class AdminDashboardController extends Controller
{
    public function index(AiService $aiService)
    {
        $activeModel = $aiService->getActiveModel();
        $totalModels = count($aiService->listModels());
        $apiPredictions = $aiService->getPredictions();

        $totalTrainings = TrainingJob::count();
        $successfulTrainings = TrainingJob::where('status', 'completed')->count();
        $failedTrainings = TrainingJob::where('status', 'failed')->count();
        $trainingSuccessRate = $totalTrainings > 0 ? round(($successfulTrainings / $totalTrainings) * 100) : 0;

        $recentPredictions = Prediction::latest()->take(5)->get();
        $recentTrainings = TrainingJob::latest()->take(5)->get();

        $todayPredictions = Prediction::whereDate('created_at', today())->count();

        return view('admin.dashboard', [
            'totalModels' => $totalModels,
            'activeModelInfo' => $activeModel['active'] ? ($activeModel['model'] ?? null) : null,
            'totalPredictions' => count($apiPredictions),
            'totalTrainings' => $totalTrainings,
            'successfulTrainings' => $successfulTrainings,
            'failedTrainings' => $failedTrainings,
            'trainingSuccessRate' => $trainingSuccessRate,
            'recentPredictions' => $recentPredictions,
            'recentTrainings' => $recentTrainings,
            'todayPredictions' => $todayPredictions,
        ]);
    }
}
