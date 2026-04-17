<div style="display:flex; flex-direction:column; gap:12px;">
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Nombre <span style="color:#ef4444;">*</span>
        </label>
        <input type="text" name="name" value="{{ old('name') }}" required
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
        @error('name') <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Email <span style="color:#ef4444;">*</span>
        </label>
        <input type="email" name="email" value="{{ old('email') }}" required
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
        @error('email') <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Rol <span style="color:#ef4444;">*</span>
        </label>
        <select name="role" required
                style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none; cursor:pointer;"
                onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
            <option value="empleado" {{ old('role', 'empleado') === 'empleado' ? 'selected' : '' }}>Empleado</option>
            <option value="turnero"  {{ old('role') === 'turnero'  ? 'selected' : '' }}>Turnero (solo box y TV)</option>
        </select>
        @error('role') <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Box / Caja
        </label>
        <input type="text" name="box_nombre" value="{{ old('box_nombre') }}"
               placeholder="Ej: Box 1, Caja 2"
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
        @error('box_nombre') <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Contraseña <span style="color:#ef4444;">*</span>
        </label>
        <input type="password" name="password" required autocomplete="new-password"
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
        @error('password') <p style="font-size:11px; color:#ef4444; margin-top:4px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
            Confirmar contraseña <span style="color:#ef4444;">*</span>
        </label>
        <input type="password" name="password_confirmation" required
               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
    </div>
</div>
