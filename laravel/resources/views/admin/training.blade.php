@extends('layouts.admin')

@section('title', 'Training')
@section('page-title', 'Training')
@section('active-training', 'bg-white/10')
@section('breadcrumb')
    <span class="text-slate-600">/</span>
    <span class="text-indigo-400 font-medium">Training</span>
@endsection

@section('content')
<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Upload New Dataset</h2>
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-900/30 border border-red-800/50 text-red-300 rounded-lg">
                @foreach($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form action="/admin/training" method="POST" enctype="multipart/form-data" id="training-form">
            @csrf
            <div class="border-2 border-dashed border-slate-600 rounded-xl p-8 text-center hover:border-indigo-500 transition cursor-pointer mb-4"
                 id="upload-zone"
                 ondragover="event.preventDefault(); this.classList.add('border-indigo-500', 'bg-indigo-900/20');"
                 ondragleave="this.classList.remove('border-indigo-500', 'bg-indigo-900/20');"
                 ondrop="event.preventDefault(); this.classList.remove('border-indigo-500', 'bg-indigo-50/30'); var files=event.dataTransfer.files; if(files.length){document.getElementById('zip-input').files=files; document.getElementById('zip-input').dispatchEvent(new Event('change'));}">
                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p class="text-slate-400" id="upload-text">Upload dataset ZIP file</p>
                <p class="text-slate-500 text-xs mt-1" id="upload-subtext">ZIP with folder structure per class (max 500MB)</p>
                <div id="file-info" class="hidden mt-4">
                    <div class="flex items-center justify-center gap-2 text-sm text-indigo-400 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span id="file-name"></span>
                        <span class="text-slate-500">|</span>
                        <span id="file-size" class="text-slate-400"></span>
                    </div>
                    <button type="button" id="cancel-file-btn" class="mt-2 text-xs text-red-400 hover:text-red-300 transition">
                        Hapus File
                    </button>
                </div>
                <input id="zip-input" type="file" name="dataset" accept=".zip" class="hidden" required>
            </div>

            {{-- Training Parameters --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Jumlah Epoch <span class="text-indigo-400 text-xs">(Rekomendasi: 10)</span></label>
                    <input type="number" name="epochs" id="input-epochs"
                           value="10" min="1"
                           class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">
                        Validation Split <span class="text-indigo-400 text-xs">(Rekomendasi: 30%)</span> <span class="text-slate-500 text-xs">| Saat ini: <span id="split-label">30%</span></span>
                    </label>
                    <input type="range" name="validation_split" id="input-split"
                           min="0.1" max="0.5" step="0.05" value="0.3"
                           class="w-full accent-indigo-600"
                           oninput="document.getElementById('split-label').textContent = (this.value * 100) + '%'">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>10%</span><span>50%</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Batch Size <span class="text-indigo-400 text-xs">(Rekomendasi: 32)</span></label>
                    <select name="batch_size"
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="16">16</option>
                        <option value="32" selected>32</option>
                        <option value="64">64</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Image Size (px) <span class="text-indigo-400 text-xs">(Rekomendasi: 224)</span></label>
                    <select name="image_size"
                            class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                        <option value="128">128 × 128</option>
                        <option value="160">160 × 160</option>
                        <option value="224" selected>224 × 224</option>
                        <option value="256">256 × 256</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Learning Rate <span class="text-indigo-400 text-xs">(Rekomendasi: 0.0001)</span></label>
                    <input type="number" name="learning_rate" id="input-lr"
                           value="0.0001" step="0.00001" min="0.00001"
                           class="w-full bg-slate-900/50 border border-slate-600 rounded-lg px-4 py-2.5 text-sm text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                </div>
            </div>

            <div id="upload-progress" class="hidden mb-4">
                <div class="flex justify-between text-sm text-slate-400 mb-1">
                    <span id="progress-text">Uploading: 0%...</span>
                    <span id="progress-pct">0%</span>
                </div>
                <div class="w-full bg-slate-700 rounded-full h-3 overflow-hidden">
                    <div id="progress-bar" class="bg-gradient-to-r from-indigo-500 to-sky-500 h-3 rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
            </div>

            <button type="submit" id="train-btn" class="w-full bg-gradient-to-r from-indigo-500 to-sky-500 text-white py-3 rounded-lg font-medium hover:shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                Start Training
            </button>
        </form>
    </div>

    <div class="bg-slate-800/60 border border-slate-700/50 rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Training History</h2>
        @if(count($jobs) === 0)
            <div class="text-center py-8">
                <p class="text-slate-400">No training sessions yet</p>
            </div>
        @else
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach($jobs as $job)
                @php
                    $status = strtolower($job->status);
                    $statusIcons = [
                        'pending' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'validating' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'extracting' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>',
                        'training' => '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
                        'completed' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'failed' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        'cancelled' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>',
                    ];
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
                <div class="block p-4 border border-slate-700 rounded-xl hover:border-indigo-500/50 hover:bg-indigo-900/20 transition relative">
                    <a href="{{ $status !== 'cancelled' ? '/admin/training/'.$job->id : '#' }}" class="block">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-mono text-slate-100">#{{ $job->id }}</span>
                                <span class="text-xs text-slate-500">{{ $job->created_at->diffForHumans() }}</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? 'bg-slate-700 text-slate-400' }}">
                                {!! $statusIcons[$status] ?? '' !!}
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 truncate">
                            <span class="text-slate-500">Dataset:</span> {{ $job->dataset_path ? \Illuminate\Support\Str::limit(basename($job->dataset_path), 40) : '-' }}
                        </p>
                        @if($status === 'completed' && $job->accuracy_result)
                            <p class="text-xs text-green-400 mt-1">Accuracy: {{ number_format($job->accuracy_result * 100, 1) }}%</p>
                        @endif
                        @if($status === 'failed' && $job->error_message)
                            <p class="text-xs text-red-400 mt-1 truncate">{{ $job->error_message }}</p>
                        @endif
                    </a>
                    @if(in_array($status, ['pending', 'validating', 'extracting', 'training']))
                    <form action="/admin/training/{{ $job->id }}/cancel" method="POST" class="mt-2 cancel-form">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition flex items-center gap-1"
                                data-id="{{ $job->id }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Batalkan
                        </button>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
    var form = document.getElementById('training-form');
    var zipInput = document.getElementById('zip-input');
    var uploadText = document.getElementById('upload-text');
    var uploadSubtext = document.getElementById('upload-subtext');
    var fileInfo = document.getElementById('file-info');
    var fileName = document.getElementById('file-name');
    var fileSize = document.getElementById('file-size');
    var cancelFileBtn = document.getElementById('cancel-file-btn');
    var uploadZone = document.getElementById('upload-zone');
    var trainBtn = document.getElementById('train-btn');
    var uploadProgress = document.getElementById('upload-progress');
    var progressBar = document.getElementById('progress-bar');
    var progressText = document.getElementById('progress-text');
    var progressPct = document.getElementById('progress-pct');

    var xhr = null;

    function formatSize(bytes) {
        return (bytes / 1024 / 1024).toFixed(1) + ' MB';
    }

    function resetUpload() {
        if (xhr) {
            xhr.abort();
            xhr = null;
        }
        zipInput.value = '';
        uploadText.textContent = 'Upload dataset ZIP file';
        uploadSubtext.textContent = 'ZIP with folder structure per class (max 500MB)';
        fileInfo.classList.add('hidden');
        uploadProgress.classList.add('hidden');
        progressBar.style.width = '0%';
        progressText.textContent = 'Uploading: 0%...';
        progressPct.textContent = '0%';
        trainBtn.disabled = false;
        trainBtn.textContent = 'Start Training';
        uploadZone.style.pointerEvents = '';
        disableBeforeUnload();
    }

    function beforeUnloadHandler(e) {
        e.preventDefault();
        e.returnValue = '';
    }

    function enableBeforeUnload() {
        window.addEventListener('beforeunload', beforeUnloadHandler);
    }

    function disableBeforeUnload() {
        window.removeEventListener('beforeunload', beforeUnloadHandler);
    }

    zipInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            var file = this.files[0];
            if (!file.name.toLowerCase().endsWith('.zip')) {
                showToast('Only ZIP files are allowed.', 'error');
                resetUpload();
                return;
            }
            uploadText.textContent = 'File selected';
            uploadSubtext.textContent = file.name + ' (' + formatSize(file.size) + ')';
            fileName.textContent = file.name;
            fileSize.textContent = formatSize(file.size);
            fileInfo.classList.remove('hidden');
            trainBtn.disabled = false;
        }
    });

    cancelFileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (xhr) return;
        resetUpload();
    });

    uploadZone.addEventListener('click', function() {
        if (xhr) return;
        zipInput.click();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var file = zipInput.files[0];
        if (!file) return;

        if (!file.name.toLowerCase().endsWith('.zip')) {
            showToast('Only ZIP files are allowed.', 'error');
            resetUpload();
            return;
        }

        trainBtn.disabled = true;
        trainBtn.textContent = 'Uploading...';
        uploadZone.style.pointerEvents = 'none';
        uploadProgress.classList.remove('hidden');
        enableBeforeUnload();

        var fd = new FormData();
        fd.append('dataset', file);
        fd.append('_token', document.querySelector('input[name=_token]').value);
        fd.append('epochs', document.getElementById('input-epochs').value);
        fd.append('validation_split', document.getElementById('input-split').value);
        fd.append('batch_size', document.querySelector('select[name="batch_size"]').value);
        fd.append('image_size', document.querySelector('select[name="image_size"]').value);
        fd.append('learning_rate', document.getElementById('input-lr').value);

        xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(evt) {
            if (evt.lengthComputable) {
                var pct = Math.round((evt.loaded / evt.total) * 100);
                progressBar.style.width = pct + '%';
                progressText.textContent = 'Uploading: ' + pct + '%...';
                progressPct.textContent = pct + '%';
            }
        });

        xhr.addEventListener('load', function() {
            disableBeforeUnload();
            var req = this;
            xhr = null;

            var data;
            try {
                data = JSON.parse(req.responseText);
            } catch (_) {
                console.error('[MediScan AI] Server returned non-JSON response:');
                console.error('  Status:', req.status);
                console.error('  Body (first 2000 chars):', req.responseText.substring(0, 2000));
                showToast('Upload failed. Server returned error page (status ' + req.status + '). Check console for details.', 'error');
                resetUpload();
                return;
            }

            if (req.status >= 200 && req.status < 300 && data.redirect) {
                window.location.href = data.redirect;
                return;
            }

            console.error('[MediScan AI] Upload error response:', data);
            showToast('Upload failed: ' + (data.error || 'Unknown error'), 'error');
            resetUpload();
        });

        xhr.addEventListener('error', function() {
            disableBeforeUnload();
            xhr = null;
            showToast('Connection lost. Upload failed.', 'error');
            resetUpload();
        });

        xhr.addEventListener('abort', function() {
            disableBeforeUnload();
            xhr = null;
            resetUpload();
        });

        xhr.open('POST', '/admin/training');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send(fd);
    });

    // ===== Cancel with confirm modal =====
    document.querySelectorAll('.cancel-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var id = this.querySelector('button').getAttribute('data-id');
            var self = this;
            showConfirm('Cancel Training', 'Yakin ingin membatalkan training #' + id + '?', function() {
                self.submit();
            });
        });
    });
})();
</script>
@endpush
