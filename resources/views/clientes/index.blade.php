<x-app-layout>
<div x-data="{
    modal: {{ $errors->any() ? 'true' : 'false' }},
    modalEditar: false,
    clienteEditar: {},
    modalEliminar: false,
    eliminarUrl: '',
    eliminarNombre: '',
    abrirEditar(id, nombre_completo, nro_socio, celular, direccion) {
        this.clienteEditar = { id, nombre_completo, nro_socio, celular, direccion };
        this.modalEditar = true;
    },
    confirmarEliminar(url, nombre) {
        this.eliminarUrl = url;
        this.eliminarNombre = nombre;
        this.modalEliminar = true;
    },
    ejecutarEliminar() {
        this.$refs.formEliminar.action = this.eliminarUrl;
        this.$refs.formEliminar.submit();
    }
}">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:#fafafa; margin:0;">Clientes</h1>
        <button @click="modal = true"
                style="background:#18181b; border:1px solid #3f3f46; color:#fafafa; padding:8px 16px;
                       border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;
                       display:flex; align-items:center; gap:6px; transition:all 0.15s;">
            + Nuevo cliente
        </button>
    </div>

    <!-- Búsqueda -->
    <div style="display:flex; gap:10px; margin-bottom:16px; align-items:center;">
        <div style="position:relative; flex:1;">
            <svg style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#71717a; pointer-events:none;"
                 width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
            <input id="buscar" type="text" placeholder="Buscar cliente..." oninput="filtrarTabla()"
                   style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px;
                          padding:8px 12px 8px 34px; font-size:13px; color:#fafafa; outline:none;"
                   onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
        </div>
        <select id="filtroCelular" class="filtro-celular" onchange="filtrarTabla()">
            <option value="">Todos</option>
            <option value="con">Con celular</option>
            <option value="sin">Sin celular</option>
        </select>
    </div>

    <!-- Tabla -->
    <div style="background:#18181b; border:1px solid #27272a; border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#27272a;">
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Apellido</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Nombre completo</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">N° Socio</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Celular</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Dirección</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $c)
                <tr style="border-top:1px solid #27272a; transition:background 0.1s;"
                    onmouseover="this.style.background='#1f1f23'"
                    onmouseout="this.style.background=''">
                    <td style="padding:12px 20px; font-size:13px; font-weight:500; color:#fafafa;"
                        data-nombre="{{ $c->nombre_completo }}">{{ $c->apellido }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:#a1a1aa;">{{ $c->nombre_completo }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:#71717a;">{{ $c->nro_socio ?? '—' }}</td>
                    <td style="padding:12px 20px; font-size:13px;" data-celular="{{ $c->celular ?? '' }}">
                        @if($c->celular)
                            <span style="color:#22c55e; font-family:monospace;">{{ $c->celular }}</span>
                        @else
                            <span style="display:inline-flex; align-items:center; gap:4px;
                                         background:rgba(245,158,11,0.1); color:#f59e0b;
                                         border:1px solid rgba(245,158,11,0.2);
                                         padding:2px 8px; border-radius:4px; font-size:11px;">
                                ⚠ Sin celular
                            </span>
                        @endif
                    </td>
                    <td style="padding:12px 20px; font-size:13px; color:#71717a;">{{ $c->direccion ?? '—' }}</td>
                    <td style="padding:12px 20px;">
                        <div style="display:flex; align-items:center; gap:4px;">

                            <!-- Editar -->
                            <button type="button" title="Editar"
                                    @click="abrirEditar({{ $c->id }}, {{ Js::from($c->nombre_completo) }}, {{ Js::from($c->nro_socio ?? '') }}, {{ Js::from($c->celular ?? '') }}, {{ Js::from($c->direccion ?? '') }})"
                                    style="background:transparent; border:none; cursor:pointer; color:#71717a;
                                           padding:6px; border-radius:6px; transition:color 0.15s; display:flex;"
                                    onmouseover="this.style.color='#fafafa'"
                                    onmouseout="this.style.color='#71717a'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>

                            <!-- Eliminar -->
                            <button type="button" title="Eliminar"
                                    @click="confirmarEliminar('{{ route('clientes.destroy', $c) }}', {{ Js::from($c->apellido) }})"
                                    style="background:transparent; border:none; cursor:pointer; color:#71717a;
                                           padding:6px; border-radius:6px; transition:color 0.15s; display:flex;"
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
                        No hay clientes registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 20px; border-top:1px solid #27272a;">
            {{ $clientes->links() }}
        </div>
    </div>

    {{-- Form oculto para eliminar --}}
    <form x-ref="formEliminar" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ─── MODAL NUEVO ──────────────────────────────────────────────── --}}
    <div x-cloak :style="modal
                ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px;'
                : 'display:none'"
         @click.self="modal = false">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px;
                    padding:28px; width:100%; max-width:440px;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:16px; font-weight:600; color:#fafafa; margin:0;">Nuevo cliente</h3>
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

            <form method="POST" action="{{ route('clientes.store') }}">
                @csrf
                @include('clientes._form')
                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px;">
                    <button type="button" @click="modal = false"
                            style="background:transparent; color:#a1a1aa; border:1px solid #3f3f46;
                                   padding:8px 16px; border-radius:7px; font-size:13px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                            style="background:#fafafa; color:#09090b; border:none; padding:8px 16px;
                                   border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;">
                        Crear
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ─── MODAL EDITAR ──────────────────────────────────────────────── --}}
    <div x-cloak :style="modalEditar
                ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px;'
                : 'display:none'"
         @click.self="modalEditar = false">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px;
                    padding:28px; width:100%; max-width:440px;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h3 style="font-size:16px; font-weight:600; color:#fafafa; margin:0;">Editar cliente</h3>
                <button type="button" @click="modalEditar = false"
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

            <form method="POST" :action="`/clientes/${clienteEditar.id}`">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Nombre completo *</label>
                        <input type="text" name="nombre_completo" x-model="clienteEditar.nombre_completo" required
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">N° Socio</label>
                        <input type="text" name="nro_socio" x-model="clienteEditar.nro_socio"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Celular</label>
                        <input type="text" name="celular" x-model="clienteEditar.celular"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                    <div>
                        <label style="display:block; font-size:11px; font-weight:600; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">Dirección</label>
                        <input type="text" name="direccion" x-model="clienteEditar.direccion"
                               style="width:100%; background:#27272a; border:1px solid #3f3f46; border-radius:7px; padding:8px 12px; font-size:13px; color:#fafafa; outline:none;"
                               onfocus="this.style.borderColor='#71717a'" onblur="this.style.borderColor='#3f3f46'">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:24px;">
                    <button type="button" @click="modalEditar = false"
                            style="background:transparent; color:#a1a1aa; border:1px solid #3f3f46;
                                   padding:8px 16px; border-radius:7px; font-size:13px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                            style="background:#22c55e; color:#000; border:none; padding:8px 16px;
                                   border-radius:7px; font-size:13px; font-weight:600; cursor:pointer;">
                        Guardar
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ─── MODAL ELIMINAR ────────────────────────────────────────────── --}}
    <div x-cloak :style="modalEliminar
                ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.75); backdrop-filter:blur(2px); align-items:center; justify-content:center; padding:16px;'
                : 'display:none'"
         @click.self="modalEliminar = false">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px;
                    padding:32px 28px; width:100%; max-width:380px; text-align:center;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; text-align:left;">
                <h3 style="font-size:16px; font-weight:600; color:#fafafa; margin:0;">¿Eliminar cliente?</h3>
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

<script>
function filtrarTabla() {
    const buscar = document.getElementById('buscar').value.toLowerCase();
    const filtro = document.getElementById('filtroCelular').value;
    document.querySelectorAll('tbody tr').forEach(row => {
        const nombre  = row.querySelector('[data-nombre]')?.dataset.nombre.toLowerCase() || '';
        const celular = row.querySelector('[data-celular]')?.dataset.celular || '';
        const coincideNombre  = nombre.includes(buscar);
        const coincideCelular = filtro === ''    ? true
                              : filtro === 'con' ? celular !== '' && celular !== '—'
                              : filtro === 'sin' ? celular === '' || celular === '—'
                              : true;
        row.style.display = (coincideNombre && coincideCelular) ? '' : 'none';
    });
}
</script>
</x-app-layout>