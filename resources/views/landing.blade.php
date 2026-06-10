<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>FICCT Portal | Inscripciones</title>
<script src="https://cdn.tailwindcss.com?plugins=forms"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet"/>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    'ficct-primary':   '#001e40',
                    'ficct-container': '#003366',
                    'ficct-accent':    '#799dd6',
                    'ficct-surface':   '#f7f9fb',
                },
                fontFamily: { sans: ['Inter', 'sans-serif'] },
            }
        }
    }
</script>
<style>
    body { font-family: 'Inter', sans-serif; }
    .hero-gradient { background: radial-gradient(circle at 50% 50%, #001e40 0%, #000a1a 100%); }
    .glass-card {
        background: rgba(255,255,255,0.04);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,0.10);
    }
    .shimmer {
        background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.06) 50%, rgba(255,255,255,0) 100%);
        background-size: 200% 100%;
        animation: shimmer 3s infinite;
    }
    @keyframes shimmer { 0%{background-position:-200% 0} 100%{background-position:200% 0} }
    .counter-animate { font-variant-numeric: tabular-nums; }
    .material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }
    nav.scrolled { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
</style>
</head>
<body class="bg-ficct-surface text-gray-900 overflow-x-hidden">

{{-- ── Navbar ───────────────────────────────────────────────────────────────── --}}
<nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-b border-gray-200 transition-shadow">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-xl font-bold text-ficct-primary tracking-tight">FICCT Portal</a>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="text-sm font-medium text-gray-600 hover:text-ficct-primary px-4 py-2 rounded-lg transition hover:bg-gray-100">
                Iniciar Sesión
            </a>
            <a href="{{ route('registro') }}"
               class="text-sm font-semibold text-white bg-ficct-primary hover:bg-ficct-container px-5 py-2 rounded-lg transition shadow-sm">
                Inscribirme
            </a>
        </div>
    </div>
</nav>

<main class="pt-16">

    {{-- ── Hero ────────────────────────────────────────────────────────────── --}}
    <section class="hero-gradient min-h-[90vh] flex items-center justify-center py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full flex flex-col md:flex-row items-center gap-16">
            <div class="md:w-1/2 space-y-7">
                {{-- Badge --}}
                @if($gestion)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white/10 border border-white/20 rounded-full text-xs font-semibold text-ficct-accent uppercase tracking-widest">
                    <span class="material-symbols-outlined text-base">school</span>
                    Admisiones {{ $gestion->nombre }} — Abiertas
                </div>
                @endif
                <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">
                    Forjando el futuro de la <span class="text-ficct-accent">Tecnología</span>
                </h1>
                <p class="text-lg text-gray-300 max-w-lg leading-relaxed">
                    Únete a la Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones. Liderazgo académico con visión global.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    <a href="{{ route('registro') }}"
                       class="inline-flex items-center gap-2 px-8 py-4 bg-white text-ficct-primary font-bold rounded-lg hover:bg-gray-100 transition shadow-lg group">
                        Empezar Inscripción
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                    </a>
                    <a href="#carreras"
                       class="inline-flex items-center gap-2 px-8 py-4 glass-card text-white font-bold rounded-lg hover:bg-white/10 transition">
                        Ver Carreras
                    </a>
                </div>
            </div>

            {{-- Hero card --}}
            <div class="md:w-1/2">
                <div class="glass-card rounded-2xl p-6 space-y-4">
                    <p class="text-white/50 text-xs font-semibold uppercase tracking-widest">Proceso de admisión</p>
                    @foreach([['1','app_registration','Datos personales','Llena tu ficha con información personal y preferencias de carrera.'],['2','upload_file','Documentos','Sube tus requisitos académicos para validación institucional.'],['3','payments','Pago de matrícula','Realiza el pago para confirmar tu inscripción.']] as [$n,$icon,$title,$desc])
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-white/5 hover:bg-white/10 transition">
                        <div class="w-10 h-10 rounded-full bg-ficct-container/60 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-ficct-accent text-lg">{{ $icon }}</span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $n }}. {{ $title }}</p>
                            <p class="text-white/60 text-xs mt-0.5">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── Pasos del proceso ───────────────────────────────────────────────── --}}
    <section class="bg-white py-24">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-bold text-ficct-primary mb-3">Proceso de Inscripción</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Sigue estos tres sencillos pasos para formalizar tu ingreso a nuestra comunidad académica.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach([
                    ['app_registration','1. Registro','Crea tu cuenta institucional y completa tus datos personales en el portal.'],
                    ['upload_file',     '2. Documentos','Sube los requisitos académicos legalizados en formato PDF o imagen para validación.'],
                    ['payments',        '3. Pago','Realiza el pago de matrícula mediante transferencia o banca móvil segura.'],
                ] as [$icon,$title,$desc])
                <div class="group p-8 rounded-2xl bg-ficct-surface border border-gray-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-50 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-ficct-primary text-3xl">{{ $icon }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-ficct-primary mb-2">{{ $title }}</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">{{ $desc }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Stats ───────────────────────────────────────────────────────────── --}}
    <section class="relative bg-ficct-primary py-20 overflow-hidden">
        <div class="absolute inset-0 shimmer opacity-10"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @foreach([['8500','Estudiantes'],['24','Laboratorios'],['45','Convenios'],['12','Postgrados']] as [$num,$label])
                <div>
                    <div class="text-4xl lg:text-5xl font-bold text-white counter-animate" data-target="{{ $num }}">0</div>
                    <div class="text-ficct-accent text-xs font-semibold uppercase tracking-widest mt-2">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Carreras ─────────────────────────────────────────────────────────── --}}
    <section id="carreras" class="py-24 bg-ficct-surface">
        <div class="max-w-7xl mx-auto px-6 lg:px-12">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-ficct-primary">Nuestras Carreras</h2>
                    <p class="text-gray-500 mt-1">Excelencia académica en constante evolución.</p>
                </div>
                <a href="{{ route('registro') }}" class="text-ficct-primary font-semibold text-sm flex items-center gap-1 hover:underline">
                    Inscribirme
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
            </div>

            @php
                $gradients = [
                    'bg-gradient-to-br from-[#001e40] to-[#003366]',
                    'bg-gradient-to-br from-[#003366] to-[#1a4a7a]',
                    'bg-gradient-to-br from-[#0a2540] to-[#1d4e89]',
                    'bg-gradient-to-br from-[#001a35] to-[#0d3060]',
                    'bg-gradient-to-br from-[#0f2b50] to-[#1e4080]',
                    'bg-gradient-to-br from-[#002050] to-[#0033a0]',
                    'bg-gradient-to-br from-[#001030] to-[#002060]',
                    'bg-gradient-to-br from-[#001845] to-[#003070]',
                ];
                $icons = ['code','wifi','developer_board','psychology','hub','storage','security','data_object'];
                $carrerasUniq = $carreras->unique('nombre')->values();
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($carrerasUniq as $i => $carrera)
                <div class="group relative rounded-2xl overflow-hidden aspect-square flex flex-col justify-end p-6 {{ $gradients[$i % count($gradients)] }} hover:scale-[1.02] transition-transform duration-300 shadow-md">
                    <div class="absolute top-5 left-5">
                        <span class="material-symbols-outlined text-white/30 text-5xl">{{ $icons[$i % count($icons)] }}</span>
                    </div>
                    <div class="relative z-10">
                        <p class="text-white font-bold text-base leading-snug">{{ $carrera->nombre }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

</main>

{{-- ── Footer ───────────────────────────────────────────────────────────────── --}}
<footer class="bg-ficct-primary text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-10">
            <div class="md:col-span-1">
                <p class="font-bold text-sm mb-4">FICCT Portal</p>
                <p class="text-white/60 text-sm leading-relaxed">
                    Institución líder en la formación de profesionales en ingeniería de Bolivia.
                </p>
            </div>
            <div>
                <p class="font-semibold text-sm mb-4">Carreras</p>
                <ul class="space-y-2 text-sm text-white/60">
                    @foreach($carreras->unique('nombre')->take(4) as $c)
                    <li>{{ $c->nombre }}</li>
                    @endforeach
                </ul>
            </div>
            <div>
                <p class="font-semibold text-sm mb-4">Portal</p>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('login') }}" class="text-white/60 hover:text-white transition">Iniciar Sesión</a></li>
                    <li><a href="{{ route('registro') }}" class="text-white/60 hover:text-white transition">Inscribirme</a></li>
                    <li><a href="{{ route('verificar-pago') }}" class="text-white/60 hover:text-white transition">Verificar Pago</a></li>
                </ul>
            </div>
            <div>
                <p class="font-semibold text-sm mb-4">Ubicación</p>
                <div class="flex items-start gap-2 text-white/60 text-sm">
                    <span class="material-symbols-outlined text-base mt-0.5">location_on</span>
                    <span>Av. Busch, Segundo Anillo, Santa Cruz de la Sierra, Bolivia.</span>
                </div>
            </div>
        </div>
        <div class="pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-white/50">
            <span>© {{ date('Y') }} Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones (FICCT)</span>
            <div class="flex gap-6">
                <a href="#" class="hover:text-white/80 transition">Privacidad</a>
                <a href="#" class="hover:text-white/80 transition">Términos de Uso</a>
            </div>
        </div>
    </div>
</footer>

<script>
// Counter animation
const counters = document.querySelectorAll('.counter-animate');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = +el.dataset.target;
        let count = 0;
        const step = Math.ceil(target / 120);
        const tick = () => {
            count = Math.min(count + step, target);
            el.textContent = count < target ? count : target + (target > 100 ? '+' : '');
            if (count < target) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
        observer.unobserve(el);
    });
}, { threshold: 0.5 });
counters.forEach(c => observer.observe(c));

// Navbar shadow on scroll
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20);
});
</script>
</body>
</html>
