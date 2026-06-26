@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
{{-- Hero --}}
<section class="relative min-h-screen flex items-center overflow-hidden pt-20">
    <div class="glow-orb w-[600px] h-[600px] bg-indigo-600/20 -top-48 -left-48 animate-pulse-glow"></div>
    <div class="glow-orb w-[500px] h-[500px] bg-cyan-600/15 top-1/2 -right-32 animate-pulse-glow" style="animation-delay: 2s"></div>
    <div class="glow-orb w-[400px] h-[400px] bg-purple-600/10 -bottom-32 left-1/4 animate-pulse-glow" style="animation-delay: 4s"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="text-center max-w-4xl mx-auto">
            <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs font-medium mb-8 tracking-wider uppercase">
                <span class="w-2 h-2 bg-indigo-400 rounded-full mr-2 animate-pulse"></span>
                Ditenagai oleh MobileNetV2 Deep Learning
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-bold leading-[1.1] tracking-tight">
                <span class="text-white">Klasifikasi</span><br>
                <span class="bg-gradient-to-r from-indigo-400 via-indigo-300 to-cyan-400 bg-clip-text text-transparent">
                    Citra Medis
                </span><br>
                <span class="text-white">Berbasis AI</span>
            </h1>

            <p class="mt-6 text-lg md:text-xl text-white/40 max-w-2xl mx-auto leading-relaxed">
                Unggah citra medis dan dapatkan hasil klasifikasi berbasis AI secara instan.
                Model deep learning kami menganalisis citra Anda dengan akurasi tinggi.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/tes"
                    class="glow-btn inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white px-8 py-3.5 rounded-xl text-lg font-semibold hover:from-indigo-400 hover:to-cyan-400 transition-all">
                    Coba Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="#features"
                    class="inline-flex items-center gap-2 border border-white/10 text-white/70 hover:text-white px-8 py-3.5 rounded-xl text-lg font-medium hover:bg-white/5 transition-all">
                    Jelajahi Fitur
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="mt-20 max-w-4xl mx-auto">
            <div class="mockup-frame relative bg-gradient-to-b from-slate-800/50 to-slate-900/50 rounded-2xl border border-white/10 p-2 md:p-3">
                <div class="absolute -inset-0.5 bg-gradient-to-r from-indigo-500/20 to-cyan-500/20 rounded-2xl blur-sm -z-10"></div>
                <div class="rounded-xl overflow-hidden bg-slate-900/80">
                    <div class="flex items-center gap-1.5 px-4 py-3 border-b border-white/5">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                        <div class="ml-4 text-white/30 text-xs font-mono">MediScan AI - Antarmuka Prediksi</div>
                    </div>
                    <div class="p-6 md:p-10 flex items-center justify-center">
                        <div class="grid md:grid-cols-2 gap-6 w-full">
                            <div class="border-2 border-dashed border-white/10 rounded-xl p-8 flex flex-col items-center justify-center text-white/30">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm">Seret dan lepaskan citra medis Anda di sini</span>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                    <div class="text-xs text-white/30 uppercase tracking-wider mb-2">Kelas yang Diprediksi</div>
                                    <div class="text-xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Melanocytic Nevus</div>
                                    <div class="mt-2 inline-flex items-center gap-2 bg-indigo-500/10 px-3 py-1 rounded-full">
                                        <span class="text-xs text-indigo-300">Tingkat Kepercayaan</span>
                                        <span class="text-sm font-bold text-indigo-300">97.3%</span>
                                    </div>
                                </div>
                                <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                    <div class="text-xs text-white/30 uppercase tracking-wider mb-3">Distribusi Probabilitas</div>
                                    <div class="space-y-2">
                                        <div><div class="flex justify-between text-xs mb-1"><span class="text-white/70">Melanocytic Nevus</span><span class="text-indigo-300">97.3%</span></div><div class="h-1.5 bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-full" style="width:97%"></div></div></div>
                                        <div><div class="flex justify-between text-xs mb-1"><span class="text-white/50">Basal Cell Carcinoma</span><span class="text-white/50">1.8%</span></div><div class="h-1.5 bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-full" style="width:2%"></div></div></div>
                                        <div><div class="flex justify-between text-xs mb-1"><span class="text-white/50">Actinic Keratosis</span><span class="text-white/50">0.9%</span></div><div class="h-1.5 bg-white/5 rounded-full overflow-hidden"><div class="h-full bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-full" style="width:1%"></div></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section id="features" class="relative py-24 md:py-32 overflow-hidden">
    <div class="glow-orb w-[400px] h-[400px] bg-indigo-600/10 top-0 right-0"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-indigo-400 text-sm font-semibold tracking-widest uppercase">Fitur Utama</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-white">
                Dibuat untuk Akurasi,
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">Didesain untuk Dampak Nyata</span>
            </h2>
            <p class="mt-4 text-white/40 text-lg">Memanfaatkan teknologi deep learning mutakhir untuk membantu tenaga medis profesional.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="glow-card rounded-2xl bg-white/[0.03] border border-white/[0.06] p-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500/20 to-indigo-500/5 border border-indigo-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Sangat Cepat</h3>
                <p class="text-white/40 text-sm leading-relaxed">Arsitektur MobileNetV2 memberikan hasil prediksi dalam hitungan milidetik dengan akurasi tinggi.</p>
            </div>

            <div class="glow-card rounded-2xl bg-white/[0.03] border border-white/[0.06] p-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500/20 to-cyan-500/5 border border-cyan-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Aman & Privat</h3>
                <p class="text-white/40 text-sm leading-relaxed">Citra medis Anda diproses secara aman dan tidak pernah disimpan tanpa izin Anda.</p>
            </div>

            <div class="glow-card rounded-2xl bg-white/[0.03] border border-white/[0.06] p-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500/20 to-purple-500/5 border border-purple-500/20 flex items-center justify-center mb-5">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Multi-Kelas</h3>
                <p class="text-white/40 text-sm leading-relaxed">Mendukung klasifikasi multi-kelas yang detail lengkap dengan skor kepercayaan pada setiap kelas.</p>
            </div>
        </div>
    </div>
</section>

{{-- How It Works --}}
<section class="relative py-24 md:py-32 overflow-hidden">
    <div class="glow-orb w-[500px] h-[500px] bg-cyan-600/10 -bottom-48 -left-48"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-cyan-400 text-sm font-semibold tracking-widest uppercase">Cara Kerja</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-white">
                Tiga Langkah Mudah
                <span class="bg-gradient-to-r from-cyan-400 to-indigo-400 bg-clip-text text-transparent">Mendapatkan Hasil</span>
            </h2>
            <p class="mt-4 text-white/40 text-lg">Proses cepat dari unggah hingga hasil analisis keluar dalam hitungan detik.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="step-number w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">1</span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">1. Unggah Gambar</h3>
                <p class="text-white/40 text-sm leading-relaxed max-w-xs mx-auto">Unggah citra medis format JPG atau PNG melalui antarmuka aman kami.</p>
            </div>

            <div class="text-center">
                <div class="step-number w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">2</span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">2. Analisis AI</h3>
                <p class="text-white/40 text-sm leading-relaxed max-w-xs mx-auto">Model deep learning MobileNetV2 kami langsung memproses dan menganalisis citra.</p>
            </div>

            <div class="text-center">
                <div class="step-number w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">3</span>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">3. Dapatkan Hasil</h3>
                <p class="text-white/40 text-sm leading-relaxed max-w-xs mx-auto">Lihat hasil prediksi kelas, skor kepercayaan, dan distribusi probabilitas secara instan.</p>
            </div>
        </div>

        <div class="text-center mt-12">
            <a href="/tes"
                class="glow-btn inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-cyan-500 text-white px-8 py-3.5 rounded-xl text-lg font-semibold hover:from-indigo-400 hover:to-cyan-400 transition-all">
                Coba Sekarang
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- Team Section --}}
<section class="relative py-24 md:py-32 overflow-hidden bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-indigo-400 text-sm font-semibold tracking-widest uppercase">Tim Kami</span>
            <h2 class="mt-4 text-3xl md:text-4xl font-bold text-white">
                Tim Pengembang
                <span class="bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">(Kelompok IV)</span>
            </h2>
            <p class="mt-4 text-white/40 text-lg">Lima anggota tim di balik pengembangan MediScan AI.</p>
        </div>

        <div class="flex flex-wrap justify-center gap-8">
            <div class="text-center w-full sm:w-[45%] md:w-[28%] lg:flex-1 max-w-[180px]" data-tilt data-tilt-glare data-tilt-max="5">
                <div class="w-36 h-44 rounded-xl bg-slate-800 mx-auto mb-4 overflow-hidden flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-2 hover:shadow-xl hover:shadow-indigo-500/30">
                    <img src="{{ asset('images/team/yenny.jpeg') }}" alt="Yeni Xavira Hati Naikofi"
                        class="w-full h-full object-cover object-top"
                        onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-indigo-400\'>Y</span>'">
                </div>
                <h3 class="text-sm font-semibold text-white">Yeni Xavira Hati Naikofi</h3>
                <p class="text-xs text-white/40 mt-1.5">51240121</p>
            </div>
            <div class="text-center w-full sm:w-[45%] md:w-[28%] lg:flex-1 max-w-[180px]" data-tilt data-tilt-glare data-tilt-max="5">
                <div class="w-36 h-44 rounded-xl bg-slate-800 mx-auto mb-4 overflow-hidden flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-2 hover:shadow-xl hover:shadow-indigo-500/30">
                    <img src="{{ asset('images/team/ben.jpeg') }}" alt="Benitrianus Nahak"
                        class="w-full h-full object-cover object-top"
                        onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-indigo-400\'>B</span>'">
                </div>
                <h3 class="text-sm font-semibold text-white">Benitrianus Nahak</h3>
                <p class="text-xs text-white/40 mt-1.5">51240127</p>
            </div>
            <div class="text-center w-full sm:w-[45%] md:w-[28%] lg:flex-1 max-w-[180px]" data-tilt data-tilt-glare data-tilt-max="5">
                <div class="w-36 h-44 rounded-xl bg-slate-800 mx-auto mb-4 overflow-hidden flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-2 hover:shadow-xl hover:shadow-indigo-500/30">
                    <img src="{{ asset('images/team/mario.jpg') }}" alt="Mario Batista Spanyola Obe"
                        class="w-full h-full object-cover object-top"
                        onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-indigo-400\'>M</span>'">
                </div>
                <h3 class="text-sm font-semibold text-white">Mario Batista Spanyola Obe</h3>
                <p class="text-xs text-white/40 mt-1.5">51240130</p>
            </div>
            <div class="text-center w-full sm:w-[45%] md:w-[28%] lg:flex-1 max-w-[180px]" data-tilt data-tilt-glare data-tilt-max="5">
                <div class="w-36 h-44 rounded-xl bg-slate-800 mx-auto mb-4 overflow-hidden flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-2 hover:shadow-xl hover:shadow-indigo-500/30">
                    <img src="{{ asset('images/team/denisa.jpeg') }}" alt="Florista Denisa Seran"
                        class="w-full h-full object-cover object-top"
                        onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-indigo-400\'>F</span>'">
                </div>
                <h3 class="text-sm font-semibold text-white">Florista Denisa Seran</h3>
                <p class="text-xs text-white/40 mt-1.5">51240148</p>
            </div>
            <div class="text-center w-full sm:w-[45%] md:w-[28%] lg:flex-1 max-w-[180px]" data-tilt data-tilt-glare data-tilt-max="5">
                <div class="w-36 h-44 rounded-xl bg-slate-800 mx-auto mb-4 overflow-hidden flex items-center justify-center transition-all duration-300 ease-out hover:scale-105 hover:-translate-y-2 hover:shadow-xl hover:shadow-indigo-500/30">
                    <img src="{{ asset('images/team/agung.jpeg') }}" alt="Basilio Agung De Fretis"
                        class="w-full h-full object-cover object-top"
                        onerror="this.style.display='none';this.parentElement.innerHTML='<span class=\'text-2xl font-bold text-indigo-400\'>B</span>'">
                </div>
                <h3 class="text-sm font-semibold text-white">Basilio Agung De Fretis</h3>
                <p class="text-xs text-white/40 mt-1.5">51240150</p>
            </div>
        </div>
    </div>
</section>

{{-- Medical Disclaimer --}}
<section class="relative pb-8 pt-4 bg-slate-900/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <p class="text-xs text-white/30 leading-relaxed max-w-3xl mx-auto">
            Referensi Sistem: Dikembangkan menggunakan arsitektur deep learning MobileNetV2 dan Convolutional Neural Network (CNN) berbasis data set medis terverifikasi. Hasil prediksi bersifat skrining awal.
        </p>
    </div>
</section>
@endsection
