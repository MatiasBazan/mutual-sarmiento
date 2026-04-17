<x-app-layout>
<div x-data="usuariosPanel()">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:#fafafa; margin:0;">Usuarios</h1>
        <button @click="abrirCrear()"
                style="background:#18181b; border:1px solid #3f3f46; color:#fafafa; padding:8px 16px;
                       border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
                       display:flex; align-items:center; gap:6px; transition:all 0.15s;">
            + Nuevo empleado
        </button>
    </div>

    <!-- Tabla -->
    <div style="background:#18181b; border:1px solid #27272a; border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#27272a;">
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Nombre</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Email</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Rol</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Box</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Estado</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $u)
                @php
                    $badge = match($u->box_status) {
                        'libre'   => ['rgba(34,197,94,0.1)',   '#22c55e', 'rgba(34,197,94,0.2)',   'LIBRE'],
                        'ocupado' => ['rgba(239,68,68,0.1)',   '#ef4444', 'rgba(239,68,68,0.2)',   'OCUPADO'],
                        'pausa'   => ['rgba(245,158,11,0.1)',  '#f59e0b', 'rgba(245,158,11,0.2)',  'EN PAUSA'],
                        default   => ['rgba(107,114,128,0.1)', '#6b7280', 'rgba(107,114,128,0.2)', 'AUSENTE'],
                    };
                @endphp
                <tr style="border-top:1px solid #27272a; transition:background 0.1s;"
                    onmouseover="this.style.background='#1f1f23'"
                    onmouseout="this.style.background=''">
                    <td style="padding:12px 20px; font-size:13px; font-weight:500; color:#fafafa;">{{ $u->name }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:#a1a1aa;">{{ $u->email }}</td>
                    <td style="padding:12px 20px;">
                        @if($u->role === 'turnero')
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:0.05em; background:rgba(139,92,246,0.1); color:#a78bfa; border:1px solid rgba(139,92,246,0.2);">
                                Turnero
                            </span>
                        @else
                            <span style="display:inline-flex; align-items:center; gap:5px; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:0.05em; background:rgba(96,165,250,0.1); color:#60a5fa; border:1px solid rgba(96,165,250,0.2);">
                                Empleado
                            </span>
                        @endif
                    </td>
                    <td style="padding:12px 20px; font-size:13px; color:#a1a1aa;">{{ $u->box_nombre ?? '—' }}</td>
                    <td style="padding:12px 20px;">
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:0.05em; background:{{ $badge[0] }}; color:{{ $badge[1] }}; border:1px solid {{ $badge[2] }};">
                            <span style="width:5px; height:5px; border-radius:50%; background:{{ $badge[1] }}; display:inline-block; flex-shrink:0;"></span>
                            {{ $badge[3] }}
                        </span>
                    </td>
                    <td style="padding:12px 20px;">
                        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">

                            <!-- Dropdown cambiar estado -->
                            <select onchange="cambiarEstadoAdmin({{ $u->id }}, this.value)"
                                    style="background:#27272a; border:1px solid #3f3f46; border-radius:6px; padding:5px 10px; font-size:11px; color:#fafafa; cursor:pointer; outline:none;">
                                <option value="libre"   {{ $u->box_status === 'libre'   ? 'selected' : '' }}>Libre</option>
                                <option value="ocupado" {{ $u->box_status === 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                                <option value="pausa"   {{ $u->box_status === 'pausa'   ? 'selected' : '' }}>Pausa</option>
                                <option value="ausente" {{ $u->box_status === 'ausente' ? 'selected' : '' }}>Ausente</option>
                            </select>

                            <!-- Editar -->
                            <button type="button" title="Editar"
                                    @click="abrirEditar({{ $u->id }}, {{ Js::from($u->name) }}, {{ Js::from($u->email) }}, {{ Js::from($u->role) }}, {{ Js::from($u->box_nombre) }})"
                                    style="background:transparent; border:none; cursor:pointer; color:#71717a; padding:6px; border-radius:6px; transition:color 0.15s; display:flex;"
                                    onmouseover="this.style.color='#fafafa'"
                                    onmouseout="this.style.color='#71717a'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <!-- Eliminar -->
                            <button type="button" title="Eliminar"
                                    @click="confirmarEliminar('{{ route('usuarios.destroy', $u) }}', {{ Js::from($u->name) }})"
                                    style="background:transparent; border:none; cursor:pointer; color:#71717a; padding:6px; border-radius:6px; transition:color 0.15s; display:flex;"
                                    onmouseover="this.style.color='#ef4444'"
                                    onmouseout="this.style.color='#71717a'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px 20px; text-align:center; font-size:13px; color:#71717a;">
                        No hay empleados. Creá el primero con el botón de arriba.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Form oculto para eliminar --}}
    <form x-ref="formEliminar" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ─── MODAL CREAR/EDITAR ────────────────────────────────────────── --}}
    <div :style="modal
                ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px;'
                : 'display:none'"
         @click.self="modal = false">
        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px;
                    padding:28px; width:100%; max-width:440px;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:16px; font-weight:600; color:#fafafa; margin:0;"
                    x-text="editandoId ? 'Editar empleado' : 'Nuevo empleado'"></h3>
                <button type="button" @click="modal = false"
                        style="background:transparent; border:1px solid #3f3f46; color:#71717a;
                               width:28px; height:28px; border-radius:6px; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; flex-shrink:0;
                               transition:all 0.15s;"
                        onmouseover="this.style.borderColor='rgba(239,68,68,0.4)'; this.style.color='#ef4444'"
                        onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#71717a'">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <!-- Form crear -->
            <form x-show="!editandoId" method="POST" action="{{ route('usuarios.store') }}">
                @csrf
                @if($errors->any() && !old('_method'))
                <div style="margin-bottom:12px; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:8px; padding:10px 14px;">
                    <ul style="margin:0; padding:0; list-style:none;">
                        @foreach($errors->all() as $error)
                        <li style="font-size:12px; color:#ef4444;">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @include('usuarios._form', ['modo' => 'crear'])
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px;">
                    <button type="button" @click="modal = false"
                            style="background:transparent; color:#a1a1aa; border:1px solid #3f3f46;
                                   padding:8px 16px; border-radius:7px; font-size:13px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                            style="background:#fafafa; color:#09090b; border:none;
                                   padding:8px 16px; border-radius:7px; font-size:13px;
                                   font-weight:600; cursor:pointer;">
                        Crear
                    </button>
                </div>
            </form>

            <!-- Forms editar -->
            @foreach($usuarios as $u)
            <form x-show="editandoId === {{ $u->id }}"
                  method="POST" action="{{ route('usuarios.update', $u) }}">
                @csrf @method('PUT')
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Nombre</label>
                        <input type="text" name="name" x-model="form.name" required
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Rol</label>
                        <select name="role" x-model="form.role" required
                                style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none; cursor:pointer;"
                                onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                            <option value="empleado">Empleado</option>
                            <option value="turnero">Turnero (solo box y TV)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Email</label>
                        <input type="email" name="email" x-model="form.email" required
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Box / Caja</label>
                        <input type="text" name="box_nombre" x-model="form.box_nombre"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                            Nueva contraseña
                            <span style="color:#71717a; font-weight:400; text-transform:none; letter-spacing:0;">(vacío = no cambiar)</span>
                        </label>
                        <input type="password" name="password" autocomplete="new-password"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px;">
                    <button type="button" @click="modal = false"
                            style="background:transparent; color:#a1a1aa; border:1px solid #3f3f46;
                                   padding:8px 16px; border-radius:7px; font-size:13px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                            style="background:#22c55e; color:#000; border:none;
                                   padding:8px 16px; border-radius:7px; font-size:13px;
                                   font-weight:600; cursor:pointer;">
                        Guardar
                    </button>
                </div>
            </form>
            @endforeach

        </div>
    </div>

    {{-- ─── MODAL ELIMINAR ────────────────────────────────────────────── --}}
    <div :style="modalEliminar
                ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px;'
                : 'display:none'"
         @click.self="modalEliminar = false">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px;
                    padding:32px 28px; width:100%; max-width:380px; text-align:center;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; text-align:left;">
                <h3 style="font-size:16px; font-weight:600; color:#fafafa; margin:0;">¿Eliminar empleado?</h3>
                <button type="button" @click="modalEliminar = false"
                        style="background:transparent; border:1px solid #3f3f46; color:#71717a;
                               width:28px; height:28px; border-radius:6px; cursor:pointer;
                               display:flex; align-items:center; justify-content:center; flex-shrink:0;
                               transition:all 0.15s;"
                        onmouseover="this.style.borderColor='rgba(239,68,68,0.4)'; this.style.color='#ef4444'"
                        onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#71717a'">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div style="width:48px; height:48px; border-radius:12px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);
                        display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>
            <p style="font-size:13px; color:#71717a; margin:0 0 24px;" x-text="`Se eliminará a ${eliminarNombre}. Esta acción no se puede deshacer.`"></p>

            <div style="display:flex; gap:8px; justify-content:center;">
                <button type="button" @click="modalEliminar = false"
                        style="background:transparent; color:#a1a1aa; border:1px solid #3f3f46;
                               padding:8px 20px; border-radius:7px; font-size:13px; cursor:pointer;">
                    Cancelar
                </button>
                <button type="button" @click="ejecutarEliminar()"
                        style="background:#ef4444; color:#fff; border:none;
                               padding:8px 20px; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;">
                    Eliminar
                </button>
            </div>

        </div>
    </div>

</div>
</x-app-layout>

<script>
async function cambiarEstadoAdmin(userId, status) {
    await fetch(`/admin/usuarios/${userId}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ status }),
    });
}

function usuariosPanel() {
    return {
        modal: {{ $errors->any() ? 'true' : 'false' }},
        editandoId: null,
        form: { name: '', email: '', role: 'empleado', box_nombre: '' },
        modalEliminar: false,
        eliminarUrl: '',
        eliminarNombre: '',

        abrirCrear() {
            this.editandoId = null;
            this.form = { name: '', email: '', role: 'empleado', box_nombre: '' };
            this.modal = true;
        },

        abrirEditar(id, name, email, role, box_nombre) {
            this.editandoId = id;
            this.form = { name, email, role, box_nombre };
            this.modal = true;
        },

        confirmarEliminar(url, nombre) {
            this.eliminarUrl = url;
            this.eliminarNombre = nombre;
            this.modalEliminar = true;
        },

        ejecutarEliminar() {
            this.$refs.formEliminar.action = this.eliminarUrl;
            this.$refs.formEliminar.submit();
        },
    };
}
</script>