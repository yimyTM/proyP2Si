{{--
  Partial: password-strength.blade.php
  Incluir debajo de cualquier <input type="password"> al que se le pase el @param $inputId
  Ejemplo: @include('partials.password-strength', ['inputId' => 'password'])
--}}

<div id="pwd-checklist-{{ $inputId }}" class="mt-2 p-3 bg-gray-50 border border-gray-200 rounded-lg hidden">
    <p class="text-xs font-medium text-gray-600 mb-2">La contraseña debe contener:</p>
    <ul class="space-y-1">
        <li id="{{ $inputId }}-len"     class="pwd-req flex items-center gap-2 text-xs text-gray-400">
            <span class="icon w-4 h-4 flex items-center justify-center rounded-full border border-current text-[9px]">✗</span>
            Mínimo 8 caracteres
        </li>
        <li id="{{ $inputId }}-upper"   class="pwd-req flex items-center gap-2 text-xs text-gray-400">
            <span class="icon w-4 h-4 flex items-center justify-center rounded-full border border-current text-[9px]">✗</span>
            Al menos una letra mayúscula (A–Z)
        </li>
        <li id="{{ $inputId }}-lower"   class="pwd-req flex items-center gap-2 text-xs text-gray-400">
            <span class="icon w-4 h-4 flex items-center justify-center rounded-full border border-current text-[9px]">✗</span>
            Al menos una letra minúscula (a–z)
        </li>
        <li id="{{ $inputId }}-digit"   class="pwd-req flex items-center gap-2 text-xs text-gray-400">
            <span class="icon w-4 h-4 flex items-center justify-center rounded-full border border-current text-[9px]">✗</span>
            Al menos un número (0–9)
        </li>
        <li id="{{ $inputId }}-special" class="pwd-req flex items-center gap-2 text-xs text-gray-400">
            <span class="icon w-4 h-4 flex items-center justify-center rounded-full border border-current text-[9px]">✗</span>
            Al menos un carácter especial (!@#$%^&*-_=+?)
        </li>
    </ul>

    {{-- Barra de fortaleza --}}
    <div class="mt-3">
        <div class="flex justify-between text-[10px] text-gray-400 mb-1">
            <span>Fortaleza:</span>
            <span id="{{ $inputId }}-label">Muy débil</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-1.5">
            <div id="{{ $inputId }}-bar" class="h-1.5 rounded-full transition-all duration-300 bg-red-400" style="width: 0%"></div>
        </div>
    </div>
</div>

<script>
(function () {
    const inputId  = '{{ $inputId }}';
    const input    = document.getElementById(inputId);
    const checklist = document.getElementById('pwd-checklist-' + inputId);
    if (!input) return;

    const rules = {
        len:     { re: /.{8,}/,                label: inputId + '-len' },
        upper:   { re: /[A-Z]/,                label: inputId + '-upper' },
        lower:   { re: /[a-z]/,                label: inputId + '-lower' },
        digit:   { re: /[0-9]/,                label: inputId + '-digit' },
        special: { re: /[!@#$%^&*\-_=+?]/,    label: inputId + '-special' },
    };

    const strengthLabels = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Muy segura'];
    const strengthColors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-blue-400', 'bg-green-500'];

    input.addEventListener('focus', () => checklist.classList.remove('hidden'));
    input.addEventListener('blur',  () => { if (!input.value) checklist.classList.add('hidden'); });

    input.addEventListener('input', function () {
        const val = this.value;
        let passed = 0;

        Object.values(rules).forEach(rule => {
            const ok  = rule.re.test(val);
            const li  = document.getElementById(rule.label);
            const ico = li.querySelector('.icon');
            if (ok) {
                li.classList.replace('text-gray-400', 'text-green-600');
                ico.textContent = '✓';
                passed++;
            } else {
                li.classList.replace('text-green-600', 'text-gray-400');
                ico.textContent = '✗';
            }
        });

        const bar   = document.getElementById(inputId + '-bar');
        const label = document.getElementById(inputId + '-label');
        const idx   = Math.max(0, passed - 1);
        bar.style.width = (passed * 20) + '%';
        bar.className   = 'h-1.5 rounded-full transition-all duration-300 ' + strengthColors[idx];
        label.textContent = strengthLabels[idx] ?? 'Muy débil';
    });
})();
</script>
