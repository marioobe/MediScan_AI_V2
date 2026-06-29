@extends('layouts.admin')

@section('title', 'Models')
@section('page-title', 'Models Management')
@section('active-models', 'bg-white/10')
@section('breadcrumb')
    <span class="text-slate-600">/</span>
    <span class="text-indigo-400 font-medium">Models</span>
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="relative flex-1 max-w-xs">
            <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="search-input" placeholder="Search by name or ID..."
                class="w-full pl-9 pr-4 py-2.5 bg-slate-900/50 border border-slate-600 rounded-lg text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none">
        </div>
        <button onclick="openCreateModal()"
            class="bg-gradient-to-r from-indigo-500 to-sky-500 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:shadow-lg transition flex items-center gap-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Register Model
        </button>
    </div>

    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6">
        @if(count($models) === 0)
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <p class="text-slate-400 text-lg">No models yet</p>
                <p class="text-slate-500 text-sm mt-1">Start a training or register a model manually</p>
                <div class="flex justify-center gap-3 mt-4">
                    <a href="/admin/training" class="bg-gradient-to-r from-indigo-500 to-sky-500 text-white px-6 py-2 rounded-lg text-sm font-medium hover:shadow-lg transition">Start Training</a>
                    <button onclick="openCreateModal()" class="border border-slate-600 text-slate-300 px-6 py-2 rounded-lg text-sm font-medium hover:bg-slate-700/50 transition">Register Model</button>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700"><th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase">Name / ID</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase">Classes</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase">Accuracy</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase hidden lg:table-cell">Precision</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase hidden lg:table-cell">Recall</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase hidden lg:table-cell">F1</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase hidden md:table-cell">Loss</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-slate-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="models-tbody">
                        @foreach($models as $model)
@php
    $displayName = $model['local_name'] ?? $model['model_id'];
    $cmUrl = env('AI_SERVICE_URL', 'http://localhost:8001') . '/files/confusion_matrix/' . $model['model_id'];
    $historyUrl = env('AI_SERVICE_URL', 'http://localhost:8001') . '/files/training_history/' . $model['model_id'];
    $config = $model['training_config'] ?? [];
    $datasetName = $config['dataset_filename'] ?? '-';
    $epochsVal = $config['epochs'] ?? '-';
    $valSplitVal = $config['validation_split'] ?? null;
    $batchSizeVal = $config['batch_size'] ?? '-';
    $imageSizeVal = $config['image_size'] ?? '-';
    $lrVal = $config['learning_rate'] ?? '-';
@endphp
                        <tr class="border-b border-slate-700/50 hover:bg-slate-700/30 transition model-row" id="model-row-{{ $model['model_id'] }}" data-name="{{ strtolower($displayName) }}" data-id="{{ $model['model_id'] }}">
                            <td class="py-3 px-4">
                                <div class="text-sm font-medium text-slate-100 model-name">{{ $displayName }}</div>
                                <div class="text-xs font-mono text-slate-500 mt-0.5">{{ $model['model_id'] }}</div>
                                @if($model['local_notes'])
                                    <div class="text-xs text-slate-500 mt-0.5 italic">{{ $model['local_notes'] }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($model['class_names'] ?? [] as $cls)
                                        <span class="inline-block px-2 py-0.5 bg-indigo-900/40 text-indigo-300 text-xs rounded-full">{{ $cls }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @if($model['accuracy'] !== null)
                                    <div class="flex items-center gap-2">
                                        <div class="w-16 bg-slate-700 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-indigo-500 to-sky-500 h-2 rounded-full" style="width: {{ $model['accuracy'] * 100 }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-slate-300">{{ number_format($model['accuracy'] * 100, 1) }}%</span>
                                    </div>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 hidden lg:table-cell">
                                <span class="text-sm text-slate-300">{{ $model['precision'] !== null ? number_format($model['precision'] * 100, 1).'%' : '-' }}</span>
                            </td>
                            <td class="py-3 px-4 hidden lg:table-cell">
                                <span class="text-sm text-slate-300">{{ $model['recall'] !== null ? number_format($model['recall'] * 100, 1).'%' : '-' }}</span>
                            </td>
                            <td class="py-3 px-4 hidden lg:table-cell">
                                <span class="text-sm text-slate-300">{{ $model['f1_score'] !== null ? number_format($model['f1_score'] * 100, 1).'%' : '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-slate-400 hidden md:table-cell">{{ $model['loss'] !== null ? number_format($model['loss'], 4) : '-' }}</td>
                            <td class="py-3 px-4">
                                @if($model['is_active'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-900/40 text-green-300">
                                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-700 text-slate-400">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if(!$model['is_active'])
                                    <form action="/admin/models/{{ $model['model_id'] }}/activate" method="POST" class="inline activate-form">
                                        @csrf
                                        <button type="submit"
                                            class="px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-sky-500 text-white rounded-lg text-xs font-medium hover:shadow-md transition">
                                            Activate
                                        </button>
                                    </form>
                                    @endif
                                    <button onclick="openInfoModal(
                                        {{ json_encode($displayName) }},
                                        {{ json_encode($model['model_id']) }},
                                        {{ json_encode($cmUrl) }},
                                        {{ json_encode($historyUrl) }},
                                        {{ $model['has_confusion_matrix'] ? 'true' : 'false' }},
                                        {{ $model['has_history'] ? 'true' : 'false' }},
                                        {{ $model['has_classification_report'] ? 'true' : 'false' }},
                                        {{ json_encode($datasetName) }},
                                        {{ json_encode($epochsVal) }},
                                        {{ json_encode($valSplitVal !== null ? ($valSplitVal * 100) . '%' : '-') }},
                                        {{ json_encode($batchSizeVal) }},
                                        {{ json_encode($imageSizeVal) }},
                                        {{ json_encode($lrVal) }}
                                    )"
                                        class="px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-sky-500 text-white rounded-lg text-xs font-medium hover:shadow-md transition">
                                        Info
                                    </button>
                                    <button onclick="openEditModal('{{ $model['model_id'] }}', '{{ addslashes($model['local_name'] ?? '') }}', '{{ addslashes($model['local_notes'] ?? '') }}')"
                                        class="p-1.5 text-indigo-400 hover:bg-indigo-900/20 rounded-lg transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <button onclick="confirmDeleteModel('{{ $model['model_id'] }}', '{{ $displayName }}')"
                                        class="p-1.5 text-red-500 hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div id="pagination" class="flex items-center justify-between mt-4 pt-4 border-t border-slate-700">
                <p class="text-sm text-slate-400" id="pagination-info"></p>
                <div class="flex gap-2">
                    <button id="prev-page" class="px-3 py-1.5 border border-slate-600 text-slate-300 rounded-lg text-xs font-medium hover:bg-slate-700/50 transition disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                    <button id="next-page" class="px-3 py-1.5 border border-slate-600 text-slate-300 rounded-lg text-xs font-medium hover:bg-slate-700/50 transition disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Info Modal --}}
<div id="info-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeInfoModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl shadow-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-700 sticky top-0 bg-slate-800 z-10">
                <h3 class="text-lg font-semibold text-white">
                    Model Info — <span id="info-title" class="text-indigo-400"></span>
                </h3>
                <button onclick="closeInfoModal()" class="p-1 text-slate-500 hover:text-slate-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-6">

                {{-- Training Parameters --}}
                <div class="bg-slate-900/40 border border-slate-700/50 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Training Parameters
                    </h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Dataset File</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-dataset">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Epochs</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-epochs">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Validation Split</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-split">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Batch Size</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-batch">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Image Size (px)</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-image">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Learning Rate</p>
                            <p class="text-sm text-slate-200 font-mono" id="info-lr">-</p>
                        </div>
                    </div>
                </div>

                {{-- Tab Navigation --}}
                <div class="border-b border-slate-700">
                    <nav class="flex gap-6 -mb-px">
                        <button onclick="switchInfoTab('cm', this)" class="info-tab text-sm font-medium pb-3 border-b-2 border-indigo-400 text-indigo-300 transition" data-tab="cm">Confusion Matrix</button>
                        <button onclick="switchInfoTab('history', this)" class="info-tab text-sm font-medium pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition" data-tab="history">Training History</button>
                        <button onclick="switchInfoTab('report', this)" class="info-tab text-sm font-medium pb-3 border-b-2 border-transparent text-slate-400 hover:text-slate-200 transition" data-tab="report">Classification Report</button>
                    </nav>
                </div>

                {{-- Tab Content: CM --}}
                <div id="info-tab-cm" class="info-tab-content">
                    <div id="cm-loading" class="text-center py-10 hidden">
                        <div class="animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                        <p class="text-slate-500 text-sm">Loading confusion matrix...</p>
                    </div>
                    <img id="cm-image" src="" alt="Confusion Matrix" class="max-h-[55vh] w-auto mx-auto hidden rounded-lg shadow-md">
                    <div id="cm-unavailable" class="text-center py-10 text-slate-500 text-sm hidden">Confusion matrix not available for this model.</div>
                    <div id="cm-analysis" class="hidden mt-6 space-y-3 text-sm"></div>
                </div>

                {{-- Tab Content: History --}}
                <div id="info-tab-history" class="info-tab-content hidden">
                    <div id="history-loading" class="text-center py-10 hidden">
                        <div class="animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                        <p class="text-slate-500 text-sm">Loading training history...</p>
                    </div>
                    <img id="history-image" src="" alt="Training History" class="max-h-[65vh] w-auto mx-auto hidden rounded-lg shadow-md">
                    <div id="history-summary" class="hidden mt-4 bg-slate-900/40 border border-slate-700/50 rounded-xl p-4 space-y-2 text-xs md:text-sm"></div>
                    <div id="history-unavailable" class="text-center py-10 text-slate-500 text-sm hidden">Training history not available for this model.</div>
                </div>

                {{-- Tab Content: Report --}}
                <div id="info-tab-report" class="info-tab-content hidden">
                    <div id="report-loading" class="text-center py-10 hidden">
                        <div class="animate-spin w-10 h-10 border-4 border-indigo-500 border-t-transparent rounded-full mx-auto mb-3"></div>
                        <p class="text-slate-500 text-sm">Loading classification report...</p>
                    </div>
                    <div id="report-content" class="hidden overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-700 text-left text-xs text-slate-400 uppercase tracking-wide">
                                    <th class="py-3 pr-4">Class</th>
                                    <th class="py-3 pr-4 text-right">Precision</th>
                                    <th class="py-3 pr-4 text-right">Recall</th>
                                    <th class="py-3 pr-4 text-right">F1-Score</th>
                                    <th class="py-3 pr-4 text-right">Support</th>
                                </tr>
                            </thead>
                            <tbody id="report-tbody"></tbody>
                        </table>
                    </div>
                    <div id="report-unavailable" class="text-center py-10 text-slate-500 text-sm hidden">Classification report not available for this model.</div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" onclick="closeEditModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full">
            <div class="flex items-center justify-between p-5 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Edit Model</h3>
                <button onclick="closeEditModal()" class="p-1 text-slate-500 hover:text-slate-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="edit-form" method="POST" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Model Name</label>
                    <input type="text" name="name" id="edit-name"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none"
                        placeholder="e.g. MobileNetV2 v2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Notes / Description</label>
                    <textarea name="notes" id="edit-notes" rows="3"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none resize-none"
                        placeholder="Optional description..."></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-700/50 transition">Cancel</button>
                    <button type="submit" id="edit-submit-btn"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-sky-500 text-white rounded-lg text-sm font-medium hover:shadow-md transition">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Create / Register Modal --}}
<div id="create-modal" class="fixed inset-0 z-50 hidden" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" onclick="closeCreateModal()"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-slate-800 rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-5 border-b border-slate-700">
                <h3 class="text-lg font-semibold text-white">Register External Model</h3>
                <button onclick="closeCreateModal()" class="p-1 text-slate-500 hover:text-slate-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @if ($errors->any())
                <div class="mx-5 mt-5 bg-red-900/30 border border-red-800/50 text-red-300 rounded-lg p-3 text-sm">
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="/admin/models/register" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Model Name</label>
                    <input type="text" name="name"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none"
                        placeholder="e.g. My Custom Model">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Model File (.keras / .h5)</label>
                    <input type="file" name="model_file" accept=".keras,.h5,.hdf5" required
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-900/40 file:text-indigo-300 hover:file:bg-indigo-800/50">
                    <p class="text-xs text-slate-500 mt-1">Max 500MB. Accepted: .keras, .h5, .hdf5</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Kelas Kategori</label>
                    <input type="text" name="class_names" required
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none"
                        placeholder="Contoh: Normal, Pneumonia, COVID-19">
                    <p class="text-xs text-slate-500 mt-1">Pisahkan dengan koma</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Akurasi Model</label>
                    <input type="number" name="accuracy" step="0.01" min="0" max="1" required
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none"
                        placeholder="0.00 - 1.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Loss Model</label>
                    <input type="number" name="loss" step="0.01" min="0" required
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none"
                        placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Gambar Confusion Matrix <span class="text-slate-500 text-xs">(Opsional)</span></label>
                    <input type="file" name="cm_file" accept=".png,.jpg,.jpeg"
                        class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-indigo-900/40 file:text-indigo-300 hover:file:bg-indigo-800/50">
                    <p class="text-xs text-slate-500 mt-1">Max 5MB. Accepted: .png, .jpg, .jpeg</p>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeCreateModal()"
                        class="px-4 py-2 border border-slate-600 text-slate-300 rounded-lg text-sm font-medium hover:bg-slate-700/50 transition">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-sky-500 text-white rounded-lg text-sm font-medium hover:shadow-md transition">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Form --}}
<form id="delete-form" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
var aiServiceUrl = '{{ env('AI_SERVICE_URL', 'localhost:8001') }}';
var currentPage = 1;
var perPage = 10;

// ===== Search + Pagination =====
function filterAndPaginate() {
    var query = document.getElementById('search-input').value.toLowerCase().trim();
    var rows = document.querySelectorAll('.model-row');
    var filtered = [];

    rows.forEach(function(row) {
        var name = row.getAttribute('data-name') || '';
        var id = row.getAttribute('data-id') || '';
        var match = name.includes(query) || id.includes(query);
        if (match) filtered.push(row);
        row.style.display = 'none';
    });

    var totalPages = Math.ceil(filtered.length / perPage) || 1;
    if (currentPage > totalPages) currentPage = totalPages;

    var start = (currentPage - 1) * perPage;
    var end = start + perPage;
    var pageItems = filtered.slice(start, end);

    pageItems.forEach(function(row) { row.style.display = ''; });

    document.getElementById('pagination-info').textContent = 'Showing ' + (start + 1) + '-' + Math.min(end, filtered.length) + ' of ' + filtered.length;

    document.getElementById('prev-page').disabled = (currentPage <= 1);
    document.getElementById('next-page').disabled = (currentPage >= totalPages);
}

document.getElementById('search-input').addEventListener('input', function() {
    currentPage = 1;
    filterAndPaginate();
});

document.getElementById('prev-page').addEventListener('click', function() {
    if (currentPage > 1) { currentPage--; filterAndPaginate(); }
});

document.getElementById('next-page').addEventListener('click', function() {
    currentPage++; filterAndPaginate();
});

// Init
filterAndPaginate();

// ===== Info Modal =====
var _infoModelId = null;

function openInfoModal(name, modelId, cmUrl, historyUrl, hasCm, hasHistory, hasReport, dataset, epochs, split, batch, image, lr) {
    _infoModelId = modelId;
    document.getElementById('info-title').textContent = name;

    // Fill training parameters
    document.getElementById('info-dataset').textContent = dataset;
    document.getElementById('info-epochs').textContent = epochs;
    document.getElementById('info-split').textContent = split;
    document.getElementById('info-batch').textContent = batch;
    document.getElementById('info-image').textContent = image;
    document.getElementById('info-lr').textContent = lr;

    // Reset tabs
    document.querySelectorAll('.info-tab-content').forEach(function(el) { el.classList.add('hidden'); });
    document.querySelectorAll('.info-tab').forEach(function(el) {
        el.classList.remove('border-indigo-400', 'text-indigo-300');
        el.classList.add('border-transparent', 'text-slate-400');
    });

    // Show first available tab
    var firstTab = document.querySelector('.info-tab');
    if (firstTab) {
        firstTab.click();
    }

    document.getElementById('info-modal').classList.remove('hidden');

    // Prepare CM tab
    var cmImg = document.getElementById('cm-image');
    cmImg.classList.add('hidden');
    document.getElementById('cm-loading').classList.add('hidden');
    document.getElementById('cm-unavailable').classList.add('hidden');
    document.getElementById('cm-analysis').classList.add('hidden');
    document.getElementById('cm-analysis').innerHTML = '';

    if (hasCm) {
        document.getElementById('cm-loading').classList.remove('hidden');
        cmImg.onload = function() {
            document.getElementById('cm-loading').classList.add('hidden');
            cmImg.classList.remove('hidden');
        };
        cmImg.onerror = function() {
            document.getElementById('cm-loading').classList.add('hidden');
            document.getElementById('cm-unavailable').classList.remove('hidden');
        };
        cmImg.src = cmUrl;

        var dataUrl = cmUrl.replace('/files/confusion_matrix/', '/files/confusion_matrix_data/');
        var xhr = new XMLHttpRequest();
        xhr.open('GET', dataUrl);
        xhr.onload = function() {
            if (xhr.status < 200 || xhr.status >= 300) return;
            try {
                var data = JSON.parse(xhr.responseText);
                renderInfoCmAnalysis(data.matrix, data.class_names);
            } catch (_) {}
        };
        xhr.send();
    } else {
        document.getElementById('cm-unavailable').classList.remove('hidden');
    }

    // Prepare History tab
    var histImg = document.getElementById('history-image');
    histImg.classList.add('hidden');
    document.getElementById('history-loading').classList.add('hidden');
    document.getElementById('history-summary').classList.add('hidden');
    document.getElementById('history-summary').innerHTML = '';
    document.getElementById('history-unavailable').classList.add('hidden');

    if (hasHistory) {
        document.getElementById('history-loading').classList.remove('hidden');
        histImg.onload = function() {
            document.getElementById('history-loading').classList.add('hidden');
            histImg.classList.remove('hidden');
        };
        histImg.onerror = function() {
            document.getElementById('history-loading').classList.add('hidden');
            document.getElementById('history-unavailable').classList.remove('hidden');
        };
        histImg.src = historyUrl;

        // Fetch raw history JSON for dynamic summary
        var histDataUrl = aiServiceUrl + '/files/training_history_data/' + modelId;
        var histXhr = new XMLHttpRequest();
        histXhr.open('GET', histDataUrl);
        histXhr.onload = function() {
            if (histXhr.status >= 200 && histXhr.status < 300) {
                try {
                    var histData = JSON.parse(histXhr.responseText);
                    renderHistorySummary(histData);
                } catch (_) {}
            }
        };
        histXhr.send();
    } else {
        document.getElementById('history-unavailable').classList.remove('hidden');
    }

    // Prepare Report tab
    document.getElementById('report-content').classList.add('hidden');
    document.getElementById('report-loading').classList.add('hidden');
    document.getElementById('report-unavailable').classList.add('hidden');

    if (hasReport) {
        document.getElementById('report-loading').classList.remove('hidden');
        var repXhr = new XMLHttpRequest();
        repXhr.open('GET', aiServiceUrl + '/files/classification_report_data/' + modelId);
        repXhr.onload = function() {
            document.getElementById('report-loading').classList.add('hidden');
            if (repXhr.status >= 200 && repXhr.status < 300) {
                try {
                    var repData = JSON.parse(repXhr.responseText);
                    renderInfoReportTable(repData);
                } catch (_) {
                    document.getElementById('report-unavailable').classList.remove('hidden');
                }
            } else {
                document.getElementById('report-unavailable').classList.remove('hidden');
            }
        };
        repXhr.onerror = function() {
            document.getElementById('report-loading').classList.add('hidden');
            document.getElementById('report-unavailable').classList.remove('hidden');
        };
        repXhr.send();
    } else {
        document.getElementById('report-unavailable').classList.remove('hidden');
    }
}

function switchInfoTab(tabId, btn) {
    document.querySelectorAll('.info-tab-content').forEach(function(el) { el.classList.add('hidden'); });
    document.querySelectorAll('.info-tab').forEach(function(el) {
        el.classList.remove('border-indigo-400', 'text-indigo-300');
        el.classList.add('border-transparent', 'text-slate-400');
    });
    document.getElementById('info-tab-' + tabId).classList.remove('hidden');
    btn.classList.remove('border-transparent', 'text-slate-400');
    btn.classList.add('border-indigo-400', 'text-indigo-300');
}

function renderInfoCmAnalysis(matrix, classNames) {
    var n = classNames.length;
    var totalGlobal = 0;
    var correctGlobal = 0;
    var rows = [];
    for (var i = 0; i < n; i++) {
        var tp = matrix[i][i];
        var rowTotal = 0;
        for (var j = 0; j < n; j++) rowTotal += matrix[i][j];
        totalGlobal += rowTotal;
        correctGlobal += tp;
        var pct = rowTotal > 0 ? ((tp / rowTotal) * 100).toFixed(1) : '0.0';
        rows.push({ label: classNames[i], tp: tp, total: rowTotal, pct: pct });
    }
    var globalAcc = totalGlobal > 0 ? ((correctGlobal / totalGlobal) * 100).toFixed(1) : '0.0';
    var html = '<div class="bg-slate-800/60 border border-slate-700/50 rounded-xl p-4 space-y-3">';
    html += '<h4 class="text-sm font-semibold text-white mb-2">Analisis Confusion Matrix</h4>';
    html += '<div class="text-xs text-slate-400">Akurasi Global: <span class="text-green-400 font-bold">' + globalAcc + '%</span> (' + correctGlobal + '/' + totalGlobal + ' sampel)</div>';
    for (var r = 0; r < rows.length; r++) {
        var row = rows[r];
        html += '<div class="text-xs text-slate-400">Kelas <span class="text-white font-medium">' + row.label + '</span>: Sensitivitas <span class="text-indigo-300 font-medium">' + row.pct + '%</span> (' + row.tp + ' dari ' + row.total + ' sampel terdeteksi dengan benar).</div>';
    }
    var warnings = [];
    for (var i = 0; i < n; i++) {
        for (var j = 0; j < n; j++) {
            if (i === j) continue;
            var rowTotal = 0;
            for (var k = 0; k < n; k++) rowTotal += matrix[i][k];
            if (rowTotal > 0) {
                var misPct = (matrix[i][j] / rowTotal) * 100;
                if (misPct > 5) {
                    warnings.push(classNames[i] + ' -> ' + classNames[j] + ' (' + misPct.toFixed(1) + '%)');
                }
            }
        }
    }
    if (warnings.length > 0) {
        html += '<div class="mt-3 p-3 bg-amber-900/30 border border-amber-700/50 rounded-lg">';
        html += '<p class="text-amber-300 text-xs font-semibold mb-1">Rekomendasi Optimasi</p>';
        html += '<p class="text-amber-400 text-xs">Misklasifikasi cukup tinggi pada:</p><ul class="list-disc list-inside text-amber-400 text-xs mt-1">';
        for (var w = 0; w < warnings.length; w++) {
            html += '<li>' + warnings[w] + ' - pertimbangkan menambah data latih untuk kelas ini.</li>';
        }
        html += '</ul></div>';
    }
    html += '</div>';
    var el = document.getElementById('cm-analysis');
    el.innerHTML = html;
    el.classList.remove('hidden');
}

function renderInfoReportTable(data) {
    var tbody = document.getElementById('report-tbody');
    var html = '';
    var orderedKeys = Object.keys(data).filter(function(k) {
        return k !== 'accuracy' && !k.startsWith('macro ') && !k.startsWith('weighted ');
    });
    orderedKeys.forEach(function(cls) {
        var m = data[cls];
        html += '<tr class="border-b border-slate-700/50">';
        html += '<td class="py-3 pr-4 text-sm font-medium text-white">' + cls + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono text-indigo-300">' + (m.precision !== undefined ? (m.precision * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono text-indigo-300">' + (m.recall !== undefined ? (m.recall * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono text-indigo-300">' + (m['f1-score'] !== undefined ? (m['f1-score'] * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono text-slate-400">' + (m.support !== undefined ? m.support : '-') + '</td>';
        html += '</tr>';
    });
    if (data['macro avg']) {
        var ma = data['macro avg'];
        html += '<tr class="bg-indigo-900/20 border-t-2 border-indigo-700">';
        html += '<td class="py-3 pr-4 text-sm font-semibold text-indigo-300">Macro Avg</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono font-semibold text-white">' + (ma.precision !== undefined ? (ma.precision * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono font-semibold text-white">' + (ma.recall !== undefined ? (ma.recall * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono font-semibold text-white">' + (ma['f1-score'] !== undefined ? (ma['f1-score'] * 100).toFixed(1) + '%' : '-') + '</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono text-slate-400">' + (ma.support !== undefined ? ma.support : '-') + '</td>';
        html += '</tr>';
    }
    if (data.accuracy !== undefined) {
        html += '<tr class="bg-green-900/20 border-t-2 border-green-700">';
        html += '<td class="py-3 pr-4 text-sm font-semibold text-green-400">Accuracy</td>';
        html += '<td class="py-3 pr-4 text-right text-sm font-mono font-semibold text-white" colspan="4">' + (data.accuracy * 100).toFixed(1) + '%</td>';
        html += '</tr>';
    }
    tbody.innerHTML = html;
    document.getElementById('report-content').classList.remove('hidden');
}

function renderHistorySummary(data) {
    var acc = data.accuracy || [];
    var valAcc = data.val_accuracy || [];
    var loss = data.loss || [];
    var valLoss = data.val_loss || [];
    var totalEpochs = acc.length;

    if (totalEpochs === 0) {
        document.getElementById('history-summary').classList.add('hidden');
        return;
    }

    // Best training accuracy
    var bestAcc = -Infinity;
    var bestAccEpoch = 0;
    for (var i = 0; i < acc.length; i++) {
        if (acc[i] > bestAcc) {
            bestAcc = acc[i];
            bestAccEpoch = i + 1;
        }
    }

    // Best validation accuracy
    var bestValAcc = -Infinity;
    var bestValAccEpoch = 0;
    for (var i = 0; i < valAcc.length; i++) {
        if (valAcc[i] > bestValAcc) {
            bestValAcc = valAcc[i];
            bestValAccEpoch = i + 1;
        }
    }

    // Final loss values
    var finalTrainLoss = loss.length > 0 ? loss[loss.length - 1] : null;
    var finalValLoss = valLoss.length > 0 ? valLoss[valLoss.length - 1] : null;

    // Overfitting check: if val_loss > train_loss by more than 20%
    var isOverfitting = false;
    var overfittingMsg = '';
    if (finalTrainLoss !== null && finalValLoss !== null && finalTrainLoss > 0) {
        var ratio = (finalValLoss - finalTrainLoss) / finalTrainLoss;
        if (ratio > 0.2) {
            isOverfitting = true;
            overfittingMsg = 'Val loss ' + (ratio * 100).toFixed(0) + '% lebih tinggi dari train loss. Model mungkin overfit. Pertimbangkan menambah data atau memperkuat regularisasi.';
        }
    }

    // Build summary HTML
    var html = '<div class="space-y-2">';

    html += '<h4 class="text-sm font-semibold text-white flex items-center gap-2">';
    html += '<svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>';
    html += 'Dynamic Chart Summary</h4>';

    html += '<div class="grid grid-cols-2 gap-3 pt-1">';

    // Total epochs
    html += '<div class="bg-slate-800/40 rounded-lg p-3">';
    html += '<p class="text-xs text-slate-500 uppercase tracking-wide">Total Epochs</p>';
    html += '<p class="text-lg font-bold text-white">' + totalEpochs + '</p>';
    html += '</div>';

    // Best training accuracy
    html += '<div class="bg-slate-800/40 rounded-lg p-3">';
    html += '<p class="text-xs text-slate-500 uppercase tracking-wide">Best Train Accuracy</p>';
    html += '<p class="text-lg font-bold text-green-400">' + (bestAcc * 100).toFixed(1) + '%</p>';
    html += '<p class="text-xs text-slate-500">Epoch ' + bestAccEpoch + '</p>';
    html += '</div>';

    // Best validation accuracy
    html += '<div class="bg-slate-800/40 rounded-lg p-3">';
    html += '<p class="text-xs text-slate-500 uppercase tracking-wide">Best Val Accuracy</p>';
    html += '<p class="text-lg font-bold text-indigo-400">' + (bestValAcc * 100).toFixed(1) + '%</p>';
    html += '<p class="text-xs text-slate-500">Epoch ' + bestValAccEpoch + '</p>';
    html += '</div>';

    // Final loss state
    html += '<div class="bg-slate-800/40 rounded-lg p-3">';
    html += '<p class="text-xs text-slate-500 uppercase tracking-wide">Final Loss</p>';
    html += '<p class="text-sm font-mono text-slate-300">Train: ' + (finalTrainLoss !== null ? finalTrainLoss.toFixed(4) : '-') + '</p>';
    html += '<p class="text-sm font-mono text-slate-300">Val: ' + (finalValLoss !== null ? finalValLoss.toFixed(4) : '-') + '</p>';
    html += '</div>';

    html += '</div>';

    if (isOverfitting) {
        html += '<div class="mt-2 p-3 bg-amber-900/30 border border-amber-700/50 rounded-lg flex items-start gap-2">';
        html += '<svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        html += '<p class="text-amber-400 text-xs">' + overfittingMsg + '</p>';
        html += '</div>';
    }

    html += '</div>';

    var el = document.getElementById('history-summary');
    el.innerHTML = html;
    el.classList.remove('hidden');
}

function closeInfoModal() {
    document.getElementById('info-modal').classList.add('hidden');
    document.getElementById('history-summary').classList.add('hidden');
    document.getElementById('history-summary').innerHTML = '';
    _infoModelId = null;
}

// ===== Edit Modal =====
var editingModelId = null;

function openEditModal(modelId, name, notes) {
    editingModelId = modelId;
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-notes').value = notes;
    document.getElementById('edit-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-modal').classList.add('hidden');
    editingModelId = null;
}

document.getElementById('edit-form').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!editingModelId) return;

    var btn = document.getElementById('edit-submit-btn');
    btn.disabled = true;
    btn.textContent = 'Saving...';

    var formData = new FormData(this);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/models/' + editingModelId + '/update');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('Accept', 'application/json');

    xhr.onload = function() {
        btn.disabled = false;
        btn.textContent = 'Save';
        if (xhr.status >= 200 && xhr.status < 300) {
            closeEditModal();
            showToast('Model updated successfully.', 'success');
            setTimeout(function() { location.reload(); }, 1000);
        } else {
            try {
                var err = JSON.parse(xhr.responseText);
                showToast('Failed: ' + (err.error || err.message || 'Unknown error'), 'error');
            } catch (_) {
                showToast('Failed to save. Refresh and try again.', 'error');
            }
        }
    };
    xhr.onerror = function() {
        btn.disabled = false;
        btn.textContent = 'Save';
        showToast('Connection lost.', 'error');
    };
    xhr.send(formData);
});

// ===== Delete =====
function confirmDeleteModel(modelId, name) {
    showConfirm(
        'Delete Model',
        'Permanently delete model "' + name + '"? All files (.keras, CM, etc.) will be removed.',
        function() { executeDelete(modelId); }
    );
}

function executeDelete(modelId) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', '/admin/models/' + modelId);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name=_token]').value);
    xhr.setRequestHeader('Accept', 'application/json');
    xhr.setRequestHeader('X-HTTP-Method-Override', 'DELETE');

    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            var row = document.getElementById('model-row-' + modelId);
            if (row) row.remove();
            showToast('Model deleted successfully.', 'success');
            filterAndPaginate();
        } else {
            try {
                var err = JSON.parse(xhr.responseText);
                showToast('Failed: ' + (err.error || err.message || 'Unknown error'), 'error');
            } catch (_) {
                showToast('Failed to delete.', 'error');
            }
        }
    };
    xhr.onerror = function() {
        showToast('Connection lost.', 'error');
    };
    xhr.send(new FormData(document.getElementById('delete-form')));
}

// ===== Activate - disable button on submit =====
document.querySelectorAll('.activate-form').forEach(function(form) {
    form.addEventListener('submit', function() {
        var btn = this.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Activating...';
    });
});

// ===== Create Modal =====
function openCreateModal() {
    document.getElementById('create-modal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('create-modal').classList.add('hidden');
}

// ===== Click outside modals =====
document.addEventListener('click', function(e) {
    var info = document.getElementById('info-modal');
    var edit = document.getElementById('edit-modal');
    var create = document.getElementById('create-modal');

    if (e.target === info) closeInfoModal();
    if (e.target === edit) closeEditModal();
    if (e.target === create) closeCreateModal();
});
</script>
@endpush
