<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Turnero — {{ config('app.name') }}</title>
    <link rel="icon" type="image/jpeg" href="/logo.jpg">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: #09090b;
            color: #fafafa;
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body x-data="turneroTv()" x-init="init()">

    <!-- Header -->
    <header style="background:#09090b; border-bottom:1px solid #27272a; padding:0 48px; height:64px; display:flex; align-items:center; justify-content:space-between; flex-shrink:0;">
        <div style="display:flex; align-items:center; gap:14px;">
            <img src="{{ asset('logo.jpg') }}" alt="{{ config('app.name') }}"
                 style="height:40px; width:auto; object-fit:contain; border-radius:6px; flex-shrink:0;">
            <div style="display:flex; flex-direction:column; gap:2px;">
                <span style="font-size:10px; color:#71717a; letter-spacing:3px; text-transform:uppercase; line-height:1;">Mutual de Amigos</span>
                <span style="font-size:17px; font-weight:700; color:#fafafa; line-height:1.3;">Club Sarmiento</span>
            </div>
        </div>
        <div style="font-family:'Courier New', monospace; font-size:32px; font-weight:700; color:#fafafa; letter-spacing:3px;"
             x-text="reloj"></div>
    </header>

    <!-- Grid de boxes -->
    <main style="flex:1; display:grid; grid-template-columns:repeat(auto-fit, minmax(320px, 1fr)); gap:16px; padding:16px; align-content:start;">
        <template x-for="box in boxes" :key="box.id">
            <div :style="`
                background: #18181b;
                border: 2px solid ${colorFor(box.box_status)};
                border-radius: 16px;
                box-shadow: 0 0 40px ${colorFor(box.box_status)}15;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 12px;
                text-align: center;
                padding: 32px;
                transition: all 0.5s;
            `">

                <!-- Punto luminoso -->
                <div :style="`
                    width: 14px;
                    height: 14px;
                    border-radius: 50%;
                    background: ${colorFor(box.box_status)};
                    box-shadow: 0 0 16px ${colorFor(box.box_status)};
                `"></div>

                <!-- Nombre del box -->
                <p :style="`
                    font-size: 13px;
                    font-weight: 500;
                    color: #71717a;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                    margin: 0;
                `" x-text="box.box_nombre"></p>

                <!-- Nombre del empleado -->
                <h2 :style="`
                    font-size: 42px;
                    font-weight: 700;
                    color: #fafafa;
                    line-height: 1;
                    margin: 0;
                `" x-text="box.name"></h2>

                <!-- Badge estado -->
                <span :style="`
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 6px 18px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: 700;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                    background: ${colorFor(box.box_status)}18;
                    color: ${colorFor(box.box_status)};
                    border: 1px solid ${colorFor(box.box_status)}40;
                `" x-text="estadoLabel(box.box_status)"></span>

            </div>
        </template>
    </main>

</body>

<script>
function turneroTv() {
    return {
        reloj: '',
        boxes: @json($usuarios),

        init() {
            this.actualizarReloj();
            setInterval(() => this.actualizarReloj(), 1000);
            this.escucharCambios();
        },

        actualizarReloj() {
            const ahora = new Date();
            this.reloj = ahora.toLocaleTimeString('es-AR', {
                hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit',
            });
        },

        colorFor(status) {
            return {
                libre:   '#22c55e',
                ocupado: '#ef4444',
                pausa:   '#f59e0b',
                ausente: '#52525b',
            }[status] ?? '#52525b';
        },

        estadoLabel(status) {
            return {
                libre:   'Libre',
                ocupado: 'Ocupado',
                pausa:   'En pausa',
                ausente: 'Ausente',
            }[status] ?? status;
        },

        escucharCambios() {
            window.Echo.channel('boxes')
                .listen('.status.updated', (data) => {
                    const idx = this.boxes.findIndex(b => b.id === data.id);
                    if (idx !== -1) this.boxes[idx] = { ...this.boxes[idx], ...data };
                });
        },
    };
}
</script>
</html>
