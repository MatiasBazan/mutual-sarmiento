<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('patch')

    <div style="display:flex; flex-direction:column; gap:12px;">

        <div>
            <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                   style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                   onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
            @error('name')
                <p style="margin-top:6px; font-size:12px; color:#ef4444;">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Correo electrónico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                   onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
            @error('email')
                <p style="margin-top:6px; font-size:12px; color:#ef4444;">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <div style="display:flex; align-items:center; gap:12px; margin-top:20px;">
        <button type="submit"
                style="background:#22c55e; color:#000; border:none; padding:8px 20px;
                       border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;">
            Guardar
        </button>
        @if(session('status') === 'profile-updated')
            <p x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
               style="font-size:13px; color:#22c55e;">Guardado.</p>
        @endif
    </div>

</form>
