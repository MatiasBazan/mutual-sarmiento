<div style="display:flex; flex-direction:column; gap:12px;">
    @foreach([
        ['apellido',        'Apellido',        'text',  'Ej: GOMEZ',        true,  true],
        ['nombre_completo', 'Nombre completo', 'text',  '',                 true,  false],
        ['nro_socio',       'N° Socio',        'text',  '0001-00026978',    false, false],
        ['celular',         'Celular',         'text',  'Ej: 3516001234',   false, false],
        ['direccion',       'Dirección',       'text',  '',                 false, false],
    ] as [$name, $label, $type, $placeholder, $required, $uppercase])
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            {{ $label }}@if($required)<span style="color:#ef4444;"> *</span>@endif
        </label>
        <input type="{{ $type }}" name="{{ $name }}" value="{{ old($name) }}"
               @if($required) required @endif
               @if($placeholder) placeholder="{{ $placeholder }}" @endif
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;{{ $uppercase ? ' text-transform:uppercase;' : '' }}"
               onfocus="this.style.borderColor='#71717a'"
               onblur="this.style.borderColor='#3f3f46'">
        @error($name) <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    @endforeach
</div>