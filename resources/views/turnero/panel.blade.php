<x-app-layout>
<div style="display:flex; justify-content:center; padding-top:24px;">
    <div x-data="miBox()" style="width:380px;">

        <div style="background:#18181b; border:1px solid #27272a; border-radius:20px; padding:48px 36px; text-align:center;">

            <!-- Box nombre -->
            <p style="font-size:10px; font-weight:600; color:#71717a; letter-spacing:4px; text-transform:uppercase; margin:0 0 8px;">
                {{ auth()->user()->box_nombre ?? 'Box' }}
            </p>

            <!-- Nombre empleado -->
            <h1 style="font-size:26px; font-weight:700; color:#fafafa; margin:0 0 24px;">
                {{ auth()->user()->name }}
            </h1>

            <!-- Badge estado actual -->
            <div style="margin-bottom:36px;">
                <span x-text="labels[estado]"
                      :style="`display:inline-flex; align-items:center; gap:6px; padding:5px 20px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; border:1px solid ${colores[estado]}44; color:${colores[estado]}; background:${colores[estado]}18;`">
                </span>
            </div>

            <!-- Separador -->
            <div style="height:1px; background:#27272a; margin-bottom:28px;"></div>

            <!-- Label -->
            <p style="font-size:10px; color:#71717a; letter-spacing:3px; text-transform:uppercase; margin:0 0 14px;">
                Cambiar estado
            </p>

            <!-- Botones -->
            <div style="display:flex; gap:8px;">
                <button @click="cambiar('libre')"
                        :style="`flex:1; height:44px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; border:1px solid ${colores.libre}44; ${estado === 'libre' ? 'background:#22c55e; color:#000; border-color:#22c55e; box-shadow:0 4px 20px rgba(34,197,94,0.3);' : 'background:rgba(34,197,94,0.08); color:#22c55e;'}`">
                    Libre
                </button>
                <button @click="cambiar('ocupado')"
                        :style="`flex:1; height:44px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; border:1px solid ${colores.ocupado}44; ${estado === 'ocupado' ? 'background:#ef4444; color:#fff; border-color:#ef4444; box-shadow:0 4px 20px rgba(239,68,68,0.3);' : 'background:rgba(239,68,68,0.08); color:#ef4444;'}`">
                    Ocupado
                </button>
                <button @click="cambiar('pausa')"
                        :style="`flex:1; height:44px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; border:1px solid ${colores.pausa}44; ${estado === 'pausa' ? 'background:#f59e0b; color:#000; border-color:#f59e0b; box-shadow:0 4px 20px rgba(245,158,11,0.3);' : 'background:rgba(245,158,11,0.08); color:#f59e0b;'}`">
                    Pausa
                </button>
                <button @click="cambiar('ausente')"
                        :style="`flex:1; height:44px; border-radius:8px; font-size:11px; font-weight:700; letter-spacing:0.05em; text-transform:uppercase; cursor:pointer; transition:all 0.2s; border:1px solid ${colores.ausente}44; ${estado === 'ausente' ? 'background:#6b7280; color:#fff; border-color:#6b7280; box-shadow:0 4px 20px rgba(107,114,128,0.3);' : 'background:rgba(107,114,128,0.08); color:#6b7280;'}`">
                    Ausente
                </button>
            </div>

            <p x-show="error" x-text="error"
               style="margin-top:16px; font-size:12px; color:#ef4444; margin-bottom:0;"></p>

        </div>
    </div>
</div>
</x-app-layout>

<script>
function miBox() {
    return {
        estado: '{{ auth()->user()->box_status ?? "libre" }}',
        error: '',
        colores: { libre: '#22c55e', ocupado: '#ef4444', pausa: '#f59e0b', ausente: '#6b7280' },
        labels:  { libre: 'LIBRE',   ocupado: 'OCUPADO', pausa: 'EN PAUSA', ausente: 'AUSENTE' },

        async cambiar(nuevoEstado) {
            if (this.estado === nuevoEstado) return;
            const prev = this.estado;
            this.estado = nuevoEstado;
            this.error = '';
            try {
                const res = await fetch('{{ route('turnero.status') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ box_status: nuevoEstado }),
                });
                const json = await res.json();
                if (!json.ok) { this.estado = prev; this.error = 'Error al cambiar estado.'; }
            } catch {
                this.estado = prev; this.error = 'Error de conexión.';
            }
        },
    };
}
</script>
