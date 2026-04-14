<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<x-app-layout>
<div x-data="resumenes()" x-init="init()">

    <!-- Header -->
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#fafafa; margin:0 0 2px;">Resúmenes</h1>
            <p style="font-size:13px; color:#71717a; margin:0;">Período: {{ $periodoActual }}</p>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">

            {{-- Badge de envío en curso --}}
            <div x-show="enviando"
                 style="display:flex; align-items:center; gap:8px; background:#27272a; border:1px solid #3f3f46; border-radius:8px; padding:8px 16px;">
                <div style="width:14px; height:14px; border-radius:50%; border:2px solid #3f3f46; border-top-color:#22c55e; animation:spin 0.8s linear infinite; flex-shrink:0;"></div>
                <span style="font-size:13px; color:#a1a1aa;">Enviando resúmenes...</span>
            </div>

            {{-- Botón Importar --}}
            <a href="{{ route('resumenes.importar') }}"
               style="display:flex; align-items:center; gap:8px; background:#27272a; border:1px solid #3f3f46; color:#fafafa; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Importar PDFs
            </a>

            @php
                $hayPendientes = ($stats['pendiente'] ?? 0) > 0 || ($stats['error'] ?? 0) > 0;
                $errores = $stats['error'] ?? 0;
            @endphp

            {{-- Botón Enviar todo (verde, hay pendientes) --}}
            @if($hayPendientes)
            <button x-show="!enviando" @click="modalConfirm = true"
                    style="display:flex; align-items:center; gap:8px; background:#22c55e; border:none; color:#000; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
                Enviar todo — {{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}
                @if($errores > 0)
                <span style="background:rgba(0,0,0,0.2); padding:1px 8px; border-radius:10px; font-size:11px;">
                    reintentar {{ $errores }}
                </span>
                @endif
            </button>
            @else
            {{-- Botón deshabilitado (sin pendientes) --}}
            <button disabled
                    style="display:flex; align-items:center; gap:8px; background:#27272a; border:1px solid #3f3f46; color:#52525b; padding:8px 16px; border-radius:8px; font-size:13px; cursor:not-allowed;">
                Sin pendientes
            </button>
            @endif

        </div>
    </div>

    <!-- Stats -->
    <div style="display:grid; grid-template-columns:repeat(5,1fr); gap:12px; margin-bottom:24px;">
        <div style="background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
            <p style="font-size:11px; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Pendientes</p>
            <p style="font-size:28px; font-weight:700; color:#fafafa; margin:0;">{{ $stats['pendiente'] ?? 0 }}</p>
        </div>
        <div style="background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
            <p style="font-size:11px; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Enviando</p>
            <p style="font-size:28px; font-weight:700; color:#60a5fa; margin:0;">{{ $stats['enviando'] ?? 0 }}</p>
        </div>
        <div style="background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
            <p style="font-size:11px; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Enviados</p>
            <p style="font-size:28px; font-weight:700; color:#22c55e; margin:0;">{{ $stats['notificado'] ?? 0 }}</p>
        </div>
        <div style="background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
            <p style="font-size:11px; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Errores</p>
            <p style="font-size:28px; font-weight:700; color:#ef4444; margin:0;">{{ $stats['error'] ?? 0 }}</p>
        </div>
        <div style="background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
            <p style="font-size:11px; color:#71717a; text-transform:uppercase; letter-spacing:0.05em; margin:0 0 4px;">Sin celular</p>
            <p style="font-size:28px; font-weight:700; color:#a1a1aa; margin:0;">{{ $stats['sin_celular'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Barra de progreso -->
    <div x-show="enviando || progreso.total > 0"
         style="margin-bottom:24px; background:#18181b; border:1px solid #27272a; border-radius:10px; padding:16px 20px;">
        <div style="display:flex; justify-content:space-between; font-size:13px; color:#a1a1aa; margin-bottom:8px;">
            <span>Progreso del envío</span>
            <span x-text="`${progreso.procesados} / ${progreso.total}`"></span>
        </div>
        <div style="width:100%; background:#27272a; border-radius:99px; height:6px;">
            <div style="background:#6366f1; height:6px; border-radius:99px; transition:width 0.3s;"
                 :style="`width:${progreso.porcentaje}%`"></div>
        </div>
        <p x-show="progreso.clienteNombre" style="font-size:12px; color:#71717a; margin-top:8px; margin-bottom:0;">
            Último: <span x-text="progreso.clienteNombre" style="color:#a1a1aa;"></span>
            — <span x-text="progreso.estado" style="font-weight:600;"></span>
        </p>
    </div>

    <!-- Tabla -->
    <div style="background:#18181b; border:1px solid #27272a; border-radius:12px; overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="background:#27272a;">
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Cliente</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">N° Socio</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Celular</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Estado</th>
                    <th style="padding:10px 20px; text-align:left; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">Enviado</th>
                    <th style="padding:10px 20px; text-align:center; font-size:11px; font-weight:600; color:#71717a; letter-spacing:0.05em; text-transform:uppercase;">PDF</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resumenes as $r)
                @php
                    $bs = match($r->estado) {
                        'pendiente'   => ['rgba(245,158,11,0.1)',  '#f59e0b', 'rgba(245,158,11,0.2)'],
                        'enviando'    => ['rgba(96,165,250,0.1)',  '#60a5fa', 'rgba(96,165,250,0.2)'],
                        'notificado'  => ['rgba(34,197,94,0.1)',   '#22c55e', 'rgba(34,197,94,0.2)'],
                        'error'       => ['rgba(239,68,68,0.1)',   '#ef4444', 'rgba(239,68,68,0.2)'],
                        default       => ['rgba(107,114,128,0.1)', '#6b7280', 'rgba(107,114,128,0.2)'],
                    };
                @endphp
                <tr id="row-{{ $r->id }}"
                    style="border-top:1px solid #27272a; transition:background 0.1s;"
                    onmouseover="this.style.background='#1f1f23'"
                    onmouseout="this.style.background=''">
                    <td style="padding:12px 20px; font-size:13px; color:#fafafa;">{{ $r->cliente->nombre_completo }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:#a1a1aa;">{{ $r->cliente->nro_socio ?? '—' }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:#a1a1aa;">{{ $r->cliente->celular ?? '—' }}</td>
                    <td style="padding:12px 20px;">
                        <span class="estado-badge-{{ $r->id }}"
                              style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:0.05em; text-transform:uppercase; background:{{ $bs[0] }}; color:{{ $bs[1] }}; border:1px solid {{ $bs[2] }};">
                            <span style="width:5px; height:5px; border-radius:50%; background:{{ $bs[1] }}; display:inline-block; flex-shrink:0;"></span>
                            <span data-label>{{ $r->estado }}</span>
                        </span>
                    </td>
                    <td style="padding:12px 20px; font-size:12px; color:#71717a;">
                        {{ $r->enviado_at ? $r->enviado_at->format('d/m H:i') : '—' }}
                    </td>
                    <td style="padding:12px 20px; text-align:center;">
                        <div style="display:inline-flex; align-items:center; gap:6px;">
                            @if($r->pdf_path)
                            <a href="{{ route('resumenes.pdf', $r) }}"
                               target="_blank"
                               title="Ver PDF"
                               style="display:inline-flex; align-items:center; justify-content:center; color:#71717a; padding:6px; border:1px solid #3f3f46; border-radius:6px; transition:all 0.15s;"
                               onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
                               onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#71717a'">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                            @endif
                            <button
                                onclick="confirmarEliminarResumen({{ $r->id }})"
                                title="Eliminar resumen"
                                style="background:transparent; border:1px solid #3f3f46; color:#71717a; padding:6px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; transition:all 0.15s;"
                                onmouseover="this.style.borderColor='rgba(239,68,68,0.4)'; this.style.color='#ef4444'; this.style.background='rgba(239,68,68,0.08)'"
                                onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#71717a'; this.style.background='transparent'">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2" stroke-linecap="round">
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
                        No hay resúmenes para este período.
                        <a href="{{ route('resumenes.importar') }}" style="color:#a1a1aa; text-decoration:underline; margin-left:4px;">Importar PDFs</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:12px 20px; border-top:1px solid #27272a;">
            {{ $resumenes->links() }}
        </div>
    </div>

    <!-- Modal confirmación de envío — fuera de la tabla, al final del x-data wrapper -->
    {{--
        FIX: se usa :style con display:flex/none en lugar de x-show,
        para evitar el conflicto entre x-show (que inyecta display:none)
        y el display:flex que necesita el overlay para centrar el contenido.
        z-index:9999 garantiza que quede por encima del layout y sidebar.
    --}}
    <div :style="modalConfirm
                    ? 'display:flex; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:16px;'
                    : 'display:none'"
         @click.self="modalConfirm = false">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px; padding:32px; width:100%; max-width:400px; text-align:center; position:relative; z-index:10000;">

            <!-- Ícono -->
            <div style="width:52px; height:52px; border-radius:50%; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </div>

            <!-- Título -->
            <h3 style="font-size:18px; font-weight:700; color:#fafafa; margin:0 0 8px;">
                Enviar resúmenes
            </h3>

            <!-- Descripción -->
            <p style="font-size:13px; color:#71717a; line-height:1.6; margin:0 0 6px;">
                Se enviarán todos los resúmenes pendientes por WhatsApp.
            </p>
            <p style="font-size:13px; color:#71717a; margin:0 0 28px;">
                Período: <span style="color:#fafafa; font-weight:600;">{{ $periodoActual }}</span>
                &nbsp;·&nbsp;
                <span style="color:#fafafa; font-weight:600;">{{ $stats['pendiente'] ?? 0 }}</span> pendientes
                @if(($stats['error'] ?? 0) > 0)
                    &nbsp;·&nbsp;
                    <span style="color:#f59e0b; font-weight:600;">{{ $stats['error'] }}</span> a reintentar
                @endif
            </p>

            <!-- Botones -->
            <div style="display:flex; gap:10px;">
                <button @click="modalConfirm = false"
                        style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; background:transparent; color:#a1a1aa; border:1px solid #3f3f46; transition:border-color 0.15s, color 0.15s;"
                        onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
                        onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#a1a1aa'">
                    Cancelar
                </button>
                <button @click="modalConfirm = false; enviarTodos()"
                        style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:#22c55e; color:#000; border:none; transition:background 0.15s;"
                        onmouseover="this.style.background='#16a34a'"
                        onmouseout="this.style.background='#22c55e'">
                    Confirmar envío
                </button>
            </div>

        </div>
    </div>

    <!-- Modal confirmación de eliminación -->
    <div id="modalEliminarResumen"
         style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.85); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px); align-items:center; justify-content:center; padding:16px;"
         onclick="if(event.target===this) cerrarModalEliminar()">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:16px; padding:32px; width:100%; max-width:400px; text-align:center; position:relative; z-index:10000;">

            <!-- Ícono -->
            <div style="width:52px; height:52px; border-radius:50%; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                    <path d="M10 11v6M14 11v6"/>
                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                </svg>
            </div>

            <!-- Título -->
            <h3 style="font-size:18px; font-weight:700; color:#fafafa; margin:0 0 8px;">
                Eliminar resumen
            </h3>

            <!-- Descripción -->
            <p style="font-size:13px; color:#71717a; line-height:1.6; margin:0 0 28px;">
                Esta acción no se puede deshacer.<br>
                Se eliminará el resumen y el PDF asociado.
            </p>

            <!-- Botones -->
            <div style="display:flex; gap:10px;">
                <button onclick="cerrarModalEliminar()"
                        style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer; background:transparent; color:#a1a1aa; border:1px solid #3f3f46; transition:border-color 0.15s, color 0.15s;"
                        onmouseover="this.style.borderColor='#71717a'; this.style.color='#fafafa'"
                        onmouseout="this.style.borderColor='#3f3f46'; this.style.color='#a1a1aa'">
                    Cancelar
                </button>
                <button onclick="eliminarResumen()"
                        style="flex:1; padding:10px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; background:#ef4444; color:#fff; border:none; transition:background 0.15s;"
                        onmouseover="this.style.background='#dc2626'"
                        onmouseout="this.style.background='#ef4444'">
                    Eliminar
                </button>
            </div>

        </div>
    </div>

</div>
</x-app-layout>

<script>
let resumenIdAEliminar = null;

function confirmarEliminarResumen(id) {
    resumenIdAEliminar = id;
    document.getElementById('modalEliminarResumen').style.display = 'flex';
}

function cerrarModalEliminar() {
    resumenIdAEliminar = null;
    document.getElementById('modalEliminarResumen').style.display = 'none';
}

async function eliminarResumen() {
    if (!resumenIdAEliminar) return;

    await fetch(`/resumenes/${resumenIdAEliminar}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    });

    window.location.reload();
}

function resumenes() {
    return {
        modalConfirm: false,
        enviando: false,
        progreso: { total: 0, procesados: 0, porcentaje: 0, clienteNombre: '', estado: '' },

        init() {
            window.Echo.channel('resumenes')
                .listen('.progreso.updated', (data) => {
                    this.progreso = {
                        total:         data.total,
                        procesados:    data.procesados,
                        porcentaje:    data.porcentaje,
                        clienteNombre: data.cliente_nombre,
                        estado:        data.estado,
                    };
                    // Actualizar solo el texto del label para preservar el punto de color
                    const badge = document.querySelector(`.estado-badge-${data.resumen_id}`);
                    if (badge) {
                        const label = badge.querySelector('[data-label]');
                        if (label) label.textContent = data.estado;
                    }
                    if (data.procesados >= data.total && data.total > 0) this.enviando = false;
                });
        },

        async enviarTodos() {
            this.enviando = true;
            this.progreso = { total: 0, procesados: 0, porcentaje: 0, clienteNombre: '', estado: '' };
            try {
                const res = await fetch('{{ route('resumenes.enviar-todos') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    },
                });
                const json = await res.json();
                if (!res.ok) {
                    alert(json.message ?? 'Error al enviar.');
                    this.enviando = false;
                } else {
                    this.progreso.total = json.total;
                }
            } catch {
                alert('Error de conexión.');
                this.enviando = false;
            }
        },
    };
}
</script>
