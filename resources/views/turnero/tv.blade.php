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
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        [x-cloak] { display: none !important; }
        * {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        /* Ticker horizontal para el nombre de la mutual */
        .ticker {
            overflow: hidden;
            width: 800px;
            white-space: nowrap;
            line-height: 1.3;
        }
        .ticker-text {
            display: inline-block;
            font-size: 30px;
            font-weight: 700;
            color: #fafafa;
            animation: ticker-scroll 18s linear infinite;
            will-change: transform;
        }
        @keyframes ticker-scroll {
            0%   { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }
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
                <div class="ticker" style="
                      font-size: 18px;
                      color: #71717a;
                      letter-spacing: 3px;
                      text-transform: uppercase;
                      line-height: 1;
                    ">
                      <span class="ticker-text">
                        Mutual de Amigos Club Sarmiento
                      </span>
                </div>
            </div>
        </div>
        <div style="font-family:'Courier New', monospace; font-size:48px; font-weight:700; color:#fafafa; letter-spacing:4px;"
             x-text="reloj"></div>
    </header>

    <!-- Grid 2x2 fijo -->
    <main style="
        height: calc(100vh - 64px);
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 16px;
        padding: 16px;
    ">
        <template x-for="box in boxes" :key="box.id">
            <div :style="`
                background: ${colorFor(box.box_status)};
                border-radius: 16px;
                box-shadow: 0 0 40px ${colorFor(box.box_status)}40;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 16px;
                text-align: center;
                padding: 32px;
                height: 100%;
                transition: all 0.5s;
            `">

                <!-- Punto luminoso -->
                <div :style="`
                    width: 14px;
                    height: 14px;
                    border-radius: 50%;
                    background: ${textColorFor(box.box_status)};
                    box-shadow: 0 0 16px ${textColorFor(box.box_status)}80;
                `"></div>

                <!-- Nombre del box (grande) -->
                <h2 :style="`
                    font-size: clamp(2rem, 4vw, 3.5rem);
                    font-weight: 700;
                    color: ${textColorFor(box.box_status)};
                    line-height: 1;
                    margin: 0;
                `" x-text="boxLabel(box)"></h2>

                <!-- Nombre del empleado (chico) -->
                <p :style="`
                    font-size: 16px;
                    font-weight: 600;
                    color: ${textColorFor(box.box_status)};
                    letter-spacing: 3px;
                    text-transform: uppercase;
                    margin: 0;
                `" x-text="box.name"></p>

                <!-- Badge estado -->
                <span :style="`
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 10px 24px;
                    border-radius: 20px;
                    font-size: 14px;
                    font-weight: 700;
                    letter-spacing: 2px;
                    text-transform: uppercase;
                    background: ${textColorFor(box.box_status)};
                    color: ${colorFor(box.box_status)};
                    border: 1px solid ${textColorFor(box.box_status)};
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

        // Texto/íconos siempre blancos sobre el fondo de color del estado.
        textColorFor() {
            return '#ffffff';
        },

        boxLabel(box) {
            if (box.role === 'admin' || !box.box_nombre) return 'GERENCIA';
            return box.box_nombre.toUpperCase();
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
