@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('active-dashboard', 'bg-white/10')
@section('breadcrumb')
    <span class="text-slate-600">/</span>
    <span class="text-indigo-400 font-medium">Dashboard</span>
@endsection

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-slate-400 font-medium uppercase tracking-wide">Total Models</p>
                <p class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $totalModels }}</p>
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-indigo-900/50 to-indigo-800/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            </div>
        </div>
        @if($activeModelInfo)
        <p class="text-xs text-slate-500 mt-2 truncate">Active: {{ $activeModelInfo['model_id'] }}</p>
        @endif
    </div>
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-slate-400 font-medium uppercase tracking-wide">Total Predictions</p>
                <p class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $totalPredictions }}</p>
                @if($todayPredictions > 0)
                <p class="text-xs text-indigo-400 mt-1">{{ $todayPredictions }} hari ini</p>
                @endif
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-sky-900/50 to-sky-800/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-slate-400 font-medium uppercase tracking-wide">Training Sessions</p>
                <p class="text-2xl md:text-3xl font-bold text-white mt-1">{{ $totalTrainings }}</p>
                @if($totalTrainings > 0)
                <p class="text-xs text-green-400 mt-1">{{ $trainingSuccessRate }}% success rate</p>
                @endif
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-emerald-900/50 to-emerald-800/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs md:text-sm text-slate-400 font-medium uppercase tracking-wide">Active Model</p>
                @if($activeModelInfo)
                <p class="text-lg md:text-xl font-bold text-white mt-1 truncate">{{ $activeModelInfo['model_id'] }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ implode(', ', $activeModelInfo['class_names'] ?? []) }}</p>
                @else
                <p class="text-lg md:text-xl font-bold text-slate-500 mt-1">Tidak Ada</p>
                <p class="text-xs text-slate-500 mt-1">Tidak ada model aktif</p>
                @endif
            </div>
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-purple-900/50 to-purple-800/50 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6 mb-6">
    {{-- Recent Predictions --}}
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base md:text-lg font-semibold text-white">Recent Predictions</h2>
            <a href="/admin/predictions" class="text-sm text-indigo-400 hover:text-indigo-300 transition font-medium">Lihat semua &rarr;</a>
        </div>
        @if($recentPredictions->count() === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-slate-400 text-sm">Belum ada prediksi</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentPredictions as $pred)
                <div class="flex items-center justify-between p-3 bg-slate-700/30 rounded-xl hover:bg-indigo-900/20 transition">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-900/50 to-indigo-800/50 flex items-center justify-center text-xs font-bold text-indigo-400 shrink-0">
                            {{ strtoupper(substr($pred->predicted_class, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-100 truncate">{{ $pred->predicted_class }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($pred->confidence * 100, 1) }}% confidence &middot; {{ $pred->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-slate-500 shrink-0 ml-2">{{ $pred->original_name ? \Illuminate\Support\Str::limit($pred->original_name, 15) : '-' }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent Training --}}
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base md:text-lg font-semibold text-white">Recent Training</h2>
            <a href="/admin/training" class="text-sm text-indigo-400 hover:text-indigo-300 transition font-medium">Lihat semua &rarr;</a>
        </div>
        @if($recentTrainings->count() === 0)
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <p class="text-slate-400 text-sm">Belum ada sesi training</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($recentTrainings as $job)
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-900/40 text-yellow-300',
                        'validating' => 'bg-blue-900/40 text-blue-300',
                        'extracting' => 'bg-blue-900/40 text-blue-300',
                        'training' => 'bg-indigo-900/40 text-indigo-300',
                        'completed' => 'bg-green-900/40 text-green-300',
                        'failed' => 'bg-red-900/40 text-red-300',
                        'cancelled' => 'bg-slate-700 text-slate-400',
                    ];
                @endphp
                <div class="flex items-center justify-between p-3 bg-slate-700/30 rounded-xl hover:bg-indigo-900/20 transition">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-slate-700 to-slate-600 flex items-center justify-center text-xs font-mono font-bold text-slate-400 shrink-0">
                            #{{ substr($job->id, 0, 4) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-100 truncate">
                                @if($job->status === 'completed' && $job->accuracy_result)
                                    Accuracy: {{ number_format($job->accuracy_result * 100, 1) }}%
                                @else
                                    Job #{{ $job->id }}
                                @endif
                            </p>
                            <p class="text-xs text-slate-500">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-medium shrink-0 {{ $statusColors[$job->status] ?? 'bg-slate-700 text-slate-400' }}">
                        {{ ucfirst($job->status) }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Quick Actions --}}
<div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-5 md:p-6">
    <h2 class="text-base md:text-lg font-semibold text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        <a href="/admin/training" class="group p-4 border border-slate-700 rounded-xl hover:border-indigo-500/50 hover:bg-indigo-900/20 transition text-center">
            <svg class="w-8 h-8 text-indigo-400 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            <span class="text-sm font-medium text-slate-300">Start Training</span>
        </a>
        <a href="/admin/models" class="group p-4 border border-slate-700 rounded-xl hover:border-indigo-500/50 hover:bg-indigo-900/20 transition text-center">
            <svg class="w-8 h-8 text-indigo-400 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
            <span class="text-sm font-medium text-slate-300">Manage Models</span>
        </a>
        <a href="/admin/predictions" class="group p-4 border border-slate-700 rounded-xl hover:border-indigo-500/50 hover:bg-indigo-900/20 transition text-center">
            <svg class="w-8 h-8 text-indigo-400 mx-auto mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span class="text-sm font-medium text-slate-300">View Predictions</span>
        </a>
    </div>
</div>
@endsection
