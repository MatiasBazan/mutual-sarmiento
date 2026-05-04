<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold" style="color:#fafafa;">Importar PDFs</h1>
            <a href="{{ route('resumenes.index') }}" class="text-sm hover:underline" style="color:#71717a;">← Volver</a>
        </div>

        <div class="rounded-2xl shadow p-8" style="background:#18181b; border:1px solid #27272a;">
            <form method="POST" action="{{ route('resumenes.importar.store') }}" enctype="multipart/form-data"
                  x-data="importarPdfs()"
                  x-init="init()"
                  @submit="cargando = true">
                @csrf

                {{-- Contador de archivos --}}
                <template x-if="archivos.length > 0">
                    <div class="mb-3 text-xs font-medium" style="color:#71717a;">
                        <span x-text="archivos.length"></span> archivo<span x-show="archivos.length !== 1">s</span> seleccionado<span x-show="archivos.length !== 1">s</span>
                        · <span x-text="totalSizeLabel()"></span>
                    </div>
                </template>

                {{-- Drop zone --}}
                <div x-ref="dropZone"
                     @click="$refs.input.click()"
                     style="border:1px dashed #3f3f46; border-radius:12px; padding:48px; text-align:center; cursor:pointer; transition:border-color .15s, background .15s;"
                     @mouseenter="$el.style.borderColor='#22c55e'; $el.style.background='rgba(34,197,94,0.03)'"
                     @mouseleave="$el.style.borderColor='#3f3f46'; $el.style.background='transparent'">

                    {{-- Upload icon --}}
                    <div style="display:flex; justify-content:center; margin-bottom:16px;">
                        <div style="width:56px; height:56px; border-radius:12px; background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.15); display:flex; align-items:center; justify-content:center;">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="16 16 12 12 8 16"/>
                                <line x1="12" y1="12" x2="12" y2="21"/>
                                <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                            </svg>
                        </div>
                    </div>

                    <p style="font-size:15px; font-weight:600; color:#fafafa; margin-bottom:6px;">Arrastrá los PDFs aquí</p>
                    <p style="font-size:13px; color:#71717a;">o hacé clic para seleccionar</p>

                    <input type="file" name="pdfs[]" multiple accept=".pdf"
                           x-ref="input"
                           class="hidden"
                           @change="agregarArchivos($event.target.files)">
                </div>

                {{-- Lista de archivos --}}
                <template x-if="archivos.length > 0">
                    <div style="margin-top:16px; display:flex; flex-direction:column; gap:8px;">
                        <template x-for="(archivo, index) in archivos" :key="index">
                            <div style="display:flex; align-items:center; gap:12px; background:#18181b; border:1px solid #27272a; border-radius:8px; padding:12px 16px;">

                                {{-- Ícono PDF --}}
                                <div style="width:36px; height:36px; border-radius:6px; background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="9" y1="13" x2="15" y2="13"/>
                                        <line x1="9" y1="17" x2="12" y2="17"/>
                                    </svg>
                                </div>

                                {{-- Info --}}
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:13px; font-weight:600; color:#fafafa; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" x-text="archivo.name"></div>
                                    <div style="font-size:11px; color:#71717a; margin-top:2px;" x-text="formatSize(archivo.size) + ' · PDF'"></div>
                                </div>

                                {{-- Badge listo --}}
                                <div style="display:flex; align-items:center; gap:4px; background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.2); color:#22c55e; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; white-space:nowrap; flex-shrink:0;">
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Listo
                                </div>

                                {{-- Botón quitar --}}
                                <button type="button"
                                        @click="quitarArchivo(index)"
                                        style="background:transparent; border:1px solid #3f3f46; color:#71717a; padding:5px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:border-color .15s, color .15s;"
                                        @mouseenter="$el.style.borderColor='rgba(239,68,68,0.4)'; $el.style.color='#ef4444'"
                                        @mouseleave="$el.style.borderColor='#3f3f46'; $el.style.color='#71717a'">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="18" y1="6" x2="6" y2="18"/>
                                        <line x1="6" y1="6" x2="18" y2="18"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>

                @error('pdfs')
                    <p class="mt-3 text-sm" style="color:#ef4444;">{{ $message }}</p>
                @enderror

                {{-- Botón importar: visible solo con archivos --}}
                <button
                    type="submit"
                    x-show="archivos.length > 0 && !cargando"
                    x-cloak
                    style="display:block; width:100%; padding:12px 24px; background:#22c55e; color:#000; font-weight:700; font-size:14px; border:none; border-radius:8px; cursor:pointer; margin-top:16px; text-align:center; letter-spacing:0.5px;"
                    x-text="'Importar ' + archivos.length + (archivos.length === 1 ? ' resumen' : ' resúmenes')"
                ></button>

                <button
                    type="submit"
                    x-show="cargando"
                    x-cloak
                    disabled
                    style="display:block; width:100%; padding:12px 24px; background:#27272a; color:#52525b; font-size:14px; border:none; border-radius:8px; cursor:not-allowed; margin-top:16px; text-align:center;"
                >Procesando...</button>
            </form>
        </div>

        <p class="text-xs mt-4 text-center" style="color:#71717a;">
            El nombre del PDF debe ser el apellido del cliente (ej: <code style="color:#a1a1aa;">GOMEZ.pdf</code>)
        </p>
    </div>
</x-app-layout>

<script>
function importarPdfs() {
    return {
        archivos: [],   // array de File objects
        cargando: false,

        init() {
            const dropZone = this.$refs.dropZone;
            const input    = this.$refs.input;

            // bfcache reset — botón Atrás restaura estado corrupto
            window.addEventListener('pageshow', (e) => {
                if (e.persisted) {
                    this.cargando = false;
                    this.archivos = [];
                    input.value   = '';
                }
            });

            // Drag over — activar estilo
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#22c55e';
                dropZone.style.background  = 'rgba(34,197,94,0.03)';
            });

            dropZone.addEventListener('dragleave', () => {
                dropZone.style.borderColor = '#3f3f46';
                dropZone.style.background  = 'transparent';
            });

            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#3f3f46';
                dropZone.style.background  = 'transparent';

                const nuevos = [...e.dataTransfer.files].filter(f => f.type === 'application/pdf');
                if (nuevos.length === 0) return;
                this._agregarYSincronizar(nuevos, input);
            });
        },

        agregarArchivos(fileList) {
            const nuevos = [...fileList].filter(f => f.type === 'application/pdf');
            if (nuevos.length === 0) return;
            this._agregarYSincronizar(nuevos, this.$refs.input);
        },

        // Agrega archivos nuevos al array (evita duplicados por nombre+tamaño)
        // y sincroniza el input file via DataTransfer
        _agregarYSincronizar(nuevos, input) {
            const existentes = new Set(this.archivos.map(f => f.name + f.size));
            nuevos.forEach(f => {
                if (!existentes.has(f.name + f.size)) this.archivos.push(f);
            });
            this._sincronizarInput(input);
        },

        quitarArchivo(index) {
            this.archivos.splice(index, 1);
            this._sincronizarInput(this.$refs.input);
        },

        _sincronizarInput(input) {
            const dt = new DataTransfer();
            this.archivos.forEach(f => dt.items.add(f));
            input.files = dt.files;
        },

        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(0) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        },

        totalSizeLabel() {
            const total = this.archivos.reduce((s, f) => s + f.size, 0);
            return this.formatSize(total) + ' total';
        },
    };
}
</script>
