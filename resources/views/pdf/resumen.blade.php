<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Resumen {{ $nro_socio ?? '' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bordo:       #6b1a2a;
            --bordo-deep:  #4a1020;
            --bordo-light: #8c2438;
            --bordo-mist:  #f7f0f2;
            --bordo-tint:  #fdf5f6;
            --gold:        #c9a96e;
            --gold-light:  #e8d5b0;
            --ink:         #1a1214;
            --ink-mid:     #4a3840;
            --ink-soft:    #8a7880;
            --ink-ghost:   #c8bcc0;
            --white:       #ffffff;
            --cream:       #fdfbf9;
            --rule:        #e8e0e3;
        }

        body {
            font-family: 'DM Sans', 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: var(--ink);
            background: var(--cream);
            padding: 28px 30px 24px;
            line-height: 1.4;
        }

        /* ══════════════════════ HEADER ══════════════════════ */
        .header-wrap {
            display: table;
            width: 100%;
            margin-bottom: 0;
        }
        .header-left {
            display: table-cell;
            width: 48%;
            vertical-align: middle;
            padding-right: 20px;
        }
        .header-right {
            display: table-cell;
            width: 52%;
            vertical-align: bottom;
            text-align: right;
        }

        .logo-row {
            display: table;
            width: 100%;
        }
        .logo-img-cell {
            display: table-cell;
            vertical-align: middle;
            width: 70px;
            padding-right: 14px;
        }
        .logo-img-cell img {
            height: 52px;
            width: auto;
        }
        .logo-text-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .logo-text-cell .org-name {
            font-size: 9.5px;
            font-weight: 600;
            color: var(--bordo);
            letter-spacing: 2px;
            text-transform: uppercase;
            line-height: 1.5;
        }
        .logo-text-cell .org-contact {
            font-size: 7px;
            color: var(--ink-soft);
            line-height: 1.8;
            margin-top: 4px;
        }

        .resumen-word {
            font-family: 'Playfair Display', 'DejaVu Sans', serif;
            font-size: 42px;
            font-weight: 900;
            color: var(--bordo);
            letter-spacing: 6px;
            line-height: 1;
            text-transform: uppercase;
        }
        .resumen-meta {
            font-size: 8px;
            color: var(--ink-soft);
            letter-spacing: 0.5px;
            margin-top: 6px;
            line-height: 1.9;
        }
        .resumen-meta strong {
            color: var(--bordo);
            font-weight: 600;
        }
        .resumen-cuit {
            font-size: 7px;
            color: var(--ink-ghost);
            margin-top: 3px;
            letter-spacing: 0.3px;
        }

        /* Línea divisoria decorativa */
        .divider-fancy {
            margin: 14px 0 16px;
            height: 2px;
            background: linear-gradient(to right, var(--bordo) 60%, var(--gold) 100%);
            border: none;
            position: relative;
        }
        .divider-fancy::after {
            content: '◆';
            position: absolute;
            right: 0;
            top: -6px;
            color: var(--gold);
            font-size: 14px;
        }

        /* ══════════════════════ CLIENTE ══════════════════════ */
        .cliente-wrap {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            background: var(--white);
            border: 1px solid var(--rule);
            border-top: 3px solid var(--bordo);
            padding: 12px 16px;
        }
        .cliente-left {
            display: table-cell;
            vertical-align: middle;
        }
        .cliente-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 140px;
        }
        .cliente-nombre {
            font-family: 'Playfair Display', 'DejaVu Sans', serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--bordo-deep);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .cliente-dato {
            font-size: 8px;
            color: var(--ink-soft);
            letter-spacing: 0.3px;
            line-height: 1.7;
        }
        .badge-label {
            font-size: 6.5px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--ink-ghost);
            margin-bottom: 4px;
        }
        .badge-nro {
            background: var(--bordo);
            color: var(--white);
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 8.5px;
            font-weight: 500;
            letter-spacing: 1px;
            padding: 5px 10px;
            display: inline-block;
        }

        /* ══════════════════════ CARDS TOTALES ══════════════════════ */
        .cards-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10px;
        }
        .card-col {
            display: table-cell;
            vertical-align: top;
            padding-right: 8px;
            width: 25%;
        }
        .card-col:last-child { padding-right: 0; }

        .card-base {
            border: 1px solid var(--rule);
            background: var(--white);
            padding: 10px 13px 11px;
            position: relative;
        }
        .card-base::before {
            content: '';
            display: block;
            height: 2px;
            background: var(--bordo);
            position: absolute;
            top: 0; left: 0; right: 0;
        }
        .card-hero {
            background: var(--bordo);
            padding: 10px 13px 11px;
            position: relative;
            overflow: hidden;
        }
        .card-hero::after {
            content: '◈';
            position: absolute;
            right: 10px;
            bottom: 4px;
            font-size: 28px;
            color: rgba(255,255,255,0.06);
        }

        .card-lbl {
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            color: var(--ink-ghost);
            margin-bottom: 7px;
        }
        .card-hero .card-lbl { color: rgba(201,169,110,0.8); }

        .card-val {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 15px;
            font-weight: 500;
            color: var(--bordo);
            letter-spacing: -0.3px;
        }
        .card-hero .card-val {
            color: var(--white);
            font-size: 16px;
        }

        /* Barra secundaria Límite / Consumo */
        .sub-bar {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .sub-cell {
            display: table-cell;
            width: 50%;
            background: var(--bordo-tint);
            border: 1px solid var(--rule);
            padding: 8px 13px;
            vertical-align: middle;
        }
        .sub-cell:first-child { border-right: none; }
        .sub-lbl {
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--ink-soft);
            font-weight: 600;
            margin-bottom: 3px;
        }
        .sub-val {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 13px;
            font-weight: 500;
            color: var(--ink-mid);
        }

        /* ══════════════════════ SECCIÓN TITLE ══════════════════════ */
        .section-head {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .section-title-text {
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--bordo);
            display: table-cell;
            vertical-align: middle;
            padding-right: 12px;
            white-space: nowrap;
        }
        .section-title-line {
            display: table-cell;
            vertical-align: middle;
            width: 100%;
            border-bottom: 1px solid var(--rule);
        }

        /* ══════════════════════ TABLA MOVIMIENTOS ══════════════════════ */
        .mov-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 8.5px;
        }
        .mov-table thead tr {
            background: var(--bordo-deep);
        }
        .mov-table thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(201,169,110,0.9);
            border: none;
        }
        .mov-table thead th.r { text-align: right; }

        .mov-table tbody tr { border-bottom: 1px solid var(--rule); }
        .mov-table tbody tr:nth-child(odd)  { background: var(--white); }
        .mov-table tbody tr:nth-child(even) { background: var(--bordo-tint); }
        .mov-table tbody tr:last-child { border-bottom: none; }

        .mov-table tbody td {
            padding: 6px 10px;
            vertical-align: middle;
            color: var(--ink);
            font-size: 8.5px;
        }
        .mov-table tbody td.mono {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 8px;
        }
        .mov-table tbody td.r { text-align: right; }

        .importe-pago  { color: #15803d; font-weight: 600; }
        .importe-cargo { color: var(--bordo); }

        .mov-tfoot td {
            padding: 7px 10px;
            background: var(--bordo-mist);
            border-top: 2px solid var(--bordo);
            font-weight: 700;
            font-size: 9px;
            color: var(--bordo-deep);
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
        }
        .mov-tfoot td.r { text-align: right; }

        /* ══════════════════════ RESUMEN PERÍODO ══════════════════════ */
        .rf-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 14px;
        }
        .rf-col {
            display: table-cell;
            width: 25%;
            padding-right: 7px;
            vertical-align: top;
        }
        .rf-col:last-child { padding-right: 0; }
        .rf-box {
            border: 1px solid var(--rule);
            background: var(--white);
            padding: 9px 12px;
            border-left: 3px solid var(--gold);
        }
        .rf-lbl {
            font-size: 6.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ink-soft);
            font-weight: 600;
            margin-bottom: 5px;
        }
        .rf-val {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 12px;
            font-weight: 500;
            color: var(--bordo);
        }

        /* ══════════════════════ AVISO ══════════════════════ */
        .aviso-box {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-left: 3px solid #16a34a;
            padding: 8px 13px;
            margin-bottom: 16px;
            font-size: 7.5px;
            color: #166534;
            line-height: 1.7;
        }

        /* ══════════════════════ CUPÓN ══════════════════════ */
        .cupon-divider {
            border: none;
            border-top: 1.5px dashed var(--ink-ghost);
            margin: 16px 0 12px;
        }
        .cupon-title {
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--ink-soft);
            text-align: center;
            margin-bottom: 10px;
        }
        .cupon-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 10px;
        }
        .cupon-table th {
            background: var(--bordo-mist);
            border: 1px solid var(--rule);
            padding: 5px 9px;
            font-size: 6.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ink-mid);
            text-align: left;
        }
        .cupon-table td {
            border: 1px solid var(--rule);
            padding: 8px 10px;
            vertical-align: middle;
        }
        .cupon-dark {
            background: var(--bordo-deep);
        }
        .cupon-mid {
            background: var(--ink-mid);
        }
        .cupon-blank {
            background: var(--white);
            height: 30px;
        }
        .cupon-lbl-white {
            font-size: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(201,169,110,0.75);
            display: block;
            margin-bottom: 3px;
        }
        .cupon-val-white {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 13px;
            font-weight: 500;
            color: var(--white);
        }
        .cupon-val-dark {
            font-family: 'DM Mono', 'DejaVu Sans Mono', monospace;
            font-size: 10px;
            font-weight: 500;
            color: var(--bordo);
        }
        .cupon-firma {
            font-size: 8px;
            color: var(--ink-mid);
            line-height: 2.5;
            letter-spacing: 0.3px;
        }
        .firma-line {
            display: inline-block;
            width: 180px;
            border-bottom: 1px solid var(--ink-mid);
        }

        /* ══════════════════════ HORARIOS / PIE ══════════════════════ */
        .horarios-box {
            width: 100%;
            text-align: center;
            border: 1px solid var(--rule);
            border-left: 4px solid var(--gold);
            background: var(--bordo-tint);
            padding: 9px 14px;
            margin: 14px 0 10px;
        }
        .horarios-box p {
            font-size: 9.5px;
            font-weight: 700;
            color: var(--bordo);
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .pie-divider {
            border: none;
            border-top: 1px solid var(--rule);
            margin-bottom: 8px;
        }
        .pie-legal {
            font-size: 6.5px;
            color: var(--ink-ghost);
            text-align: center;
            line-height: 1.8;
        }
        .pie-footer {
            display: table;
            width: 100%;
            margin-top: 6px;
        }
        .pie-left {
            display: table-cell;
            text-align: left;
            font-size: 6.5px;
            color: var(--ink-ghost);
        }
        .pie-right {
            display: table-cell;
            text-align: right;
            font-size: 6.5px;
            color: var(--ink-ghost);
        }
    </style>
</head>
<body>

{{-- ══════════════════════════════════ 1. HEADER ══════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:0;">
    <tr>
        <td style="width:50%; vertical-align:middle; padding-right:20px;">
            <table style="border-collapse:collapse; width:100%;">
                <tr>
                    <td style="vertical-align:middle; width:70px; padding-right:14px;">
                        @if(!empty($logoBase64))
                            <img src="{{ $logoBase64 }}" style="height:52px; width:auto;">
                        @else
                            <img src="/public/2.jpg" style="height:52px; width:auto;">
                        @endif
                    </td>
                    <td style="vertical-align:middle;">
                        <div style="font-size:9.5px; font-weight:600; color:#6b1a2a; letter-spacing:2px; text-transform:uppercase; line-height:1.5;">
                            MUTUAL DE AMIGOS<br>CLUB SARMIENTO
                        </div>
                        <div style="font-size:7px; color:#8a7880; line-height:1.8; margin-top:4px;">
                            Bv. Belgrano 1080 · Tel: 03472-480072 · Cel: 3472-547160<br>
                            LEONES — CÓRDOBA · IVA EXENTO<br>
                            mutualamigossarmiento@gmail.com<br>
                            Matrícula Cba. Nº 731 · Galería Sarmiento - Local 10<br>
                            CUIT: 30-52715206-9 · Ing. Brutos: Exento
                        </div>
                    </td>
                </tr>
            </table>
        </td>
        <td style="width:50%; vertical-align:bottom; text-align:right;">
            <div style="font-family:'Playfair Display','DejaVu Sans',serif; font-size:42px; font-weight:900; color:#6b1a2a; letter-spacing:6px; line-height:1; text-transform:uppercase;">RESUMEN</div>
            <div style="font-size:8px; color:#8a7880; letter-spacing:0.5px; margin-top:6px; line-height:1.9;">
                <strong style="color:#6b1a2a; font-weight:600;">N°</strong> {{ $nro_socio ?? '—' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong style="color:#6b1a2a; font-weight:600;">Período:</strong> {{ $fecha_periodo ?? '—' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong style="color:#6b1a2a; font-weight:600;">Vencimiento:</strong> {{ $fecha_vencimiento ?? '—' }}
            </div>
            <div style="font-size:7px; color:#c8bcc0; margin-top:3px;">
                CUIT: 30-52715206-9 · Ing. Brutos: Exento · ANSeS: 52715206
            </div>
        </td>
    </tr>
</table>

{{-- Divisor decorativo --}}
<table style="width:100%; border-collapse:collapse; margin:14px 0 16px;">
    <tr>
        <td style="height:2px; background:linear-gradient(to right, #6b1a2a 70%, #c9a96e 100%); padding:0;"></td>
        <td style="width:14px; text-align:right; vertical-align:middle; padding:0; font-size:14px; color:#c9a96e; padding-left:3px;">◆</td>
    </tr>
</table>

{{-- ══════════════════════════════════ 2. CLIENTE ══════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:16px; background:#ffffff; border:1px solid #e8e0e3; border-top:3px solid #6b1a2a;">
    <tr>
        <td style="padding:12px 16px; vertical-align:middle;">
            <div style="font-family:'Playfair Display','DejaVu Sans',serif; font-size:16px; font-weight:700; color:#4a1020; letter-spacing:0.5px; margin-bottom:4px;">{{ $nombre_completo ?? '—' }}</div>
            <div style="font-size:8px; color:#8a7880; letter-spacing:0.3px; line-height:1.7;">
                {{ $direccion ?? '' }}{{ !empty($localidad) ? ' — ' . $localidad : '' }}
                @if(!empty($nro_tarjeta))
                    &nbsp;&nbsp;·&nbsp;&nbsp;N° Tarjeta: {{ $nro_tarjeta }}
                @endif
            </div>
        </td>
        <td style="padding:12px 16px; vertical-align:middle; text-align:right; width:150px;">
            <div style="font-size:6.5px; letter-spacing:1.5px; text-transform:uppercase; color:#c8bcc0; margin-bottom:4px;">N° Socio</div>
            <div style="background:#6b1a2a; color:#ffffff; font-family:'DejaVu Sans Mono',monospace; font-size:8.5px; font-weight:500; letter-spacing:1px; padding:5px 10px; display:inline-block;">{{ $nro_socio ?? '—' }}</div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════ 3. CARDS TOTALES ══════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:10px;">
    <tr>
        {{-- Saldo Actual — hero card --}}
        <td style="width:25%; padding-right:8px; vertical-align:top;">
            <div style="background:#6b1a2a; padding:11px 14px; position:relative; overflow:hidden;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.5px; font-weight:700; color:rgba(201,169,110,0.85); margin-bottom:7px;">Saldo Actual</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:16px; font-weight:700; color:#ffffff; letter-spacing:-0.3px;">$ {{ $saldo_actual ?? '0.00' }}</div>
            </div>
        </td>
        {{-- Pago Mínimo --}}
        <td style="width:25%; padding-right:8px; vertical-align:top;">
            <div style="background:#ffffff; border:1px solid #e8e0e3; border-top:2px solid #6b1a2a; padding:11px 14px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.5px; font-weight:700; color:#c8bcc0; margin-bottom:7px;">Pago Mínimo</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:15px; font-weight:700; color:#6b1a2a;">$ {{ $pago_minimo ?? '0.00' }}</div>
            </div>
        </td>
        {{-- Saldo Anterior --}}
        <td style="width:25%; padding-right:8px; vertical-align:top;">
            <div style="background:#ffffff; border:1px solid #e8e0e3; border-top:2px solid #6b1a2a; padding:11px 14px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.5px; font-weight:700; color:#c8bcc0; margin-bottom:7px;">Saldo Anterior</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:15px; font-weight:700; color:#6b1a2a;">$ {{ $saldo_anterior ?? '0.00' }}</div>
            </div>
        </td>
        {{-- Vencimiento --}}
        <td style="width:25%; vertical-align:top;">
            <div style="background:#ffffff; border:1px solid #e8e0e3; border-top:2px solid #c9a96e; padding:11px 14px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.5px; font-weight:700; color:#c8bcc0; margin-bottom:7px;">Vencimiento</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:15px; font-weight:700; color:#6b1a2a;">{{ $fecha_vencimiento ?? '—' }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- Barra secundaria Límite / Consumo --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
    <tr>
        <td style="width:50%; background:#fdf5f6; border:1px solid #e8e0e3; border-right:none; padding:9px 14px; vertical-align:middle;">
            <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.2px; color:#8a7880; font-weight:600; margin-bottom:3px;">Límite de Compra</div>
            <div style="font-family:'DejaVu Sans Mono',monospace; font-size:13px; font-weight:600; color:#4a3840;">$ {{ $limite_compra ?? '0.00' }}</div>
        </td>
        <td style="width:50%; background:#fdf5f6; border:1px solid #e8e0e3; padding:9px 14px; vertical-align:middle;">
            <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1.2px; color:#8a7880; font-weight:600; margin-bottom:3px;">Consumo Actual</div>
            <div style="font-family:'DejaVu Sans Mono',monospace; font-size:13px; font-weight:600; color:#4a3840;">$ {{ $consumo_actual ?? '0.00' }}</div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════ 4. MOVIMIENTOS ══════════════════════════════════ --}}
@if(!empty($movimientos))
    {{-- Section title --}}
    <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
        <tr>
            <td style="font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:2.5px; color:#6b1a2a; white-space:nowrap; padding-right:12px; vertical-align:middle; width:1%;">Movimientos del Período</td>
            <td style="border-bottom:1px solid #e8e0e3; vertical-align:middle;"></td>
        </tr>
    </table>

    <table style="width:100%; border-collapse:collapse; margin-bottom:20px; font-size:8.5px;">
        <thead>
        <tr style="background:#4a1020;">
            <th style="padding:8px 10px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(201,169,110,0.9); width:13%;">Fecha</th>
            <th style="padding:8px 10px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(201,169,110,0.9); width:14%;">Comprobante</th>
            <th style="padding:8px 10px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(201,169,110,0.9);">Descripción</th>
            <th style="padding:8px 10px; text-align:left; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(201,169,110,0.9); width:9%;">Cuota</th>
            <th style="padding:8px 10px; text-align:right; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:rgba(201,169,110,0.9); width:17%;">Importe</th>
        </tr>
        </thead>
        <tbody>
        @php $i = 0; @endphp
        @foreach($movimientos as $mov)
            @php
                $i++;
                $esPago = str_starts_with(trim($mov['importe'] ?? ''), '-');
                $bgRow = $i % 2 === 0 ? '#fdf5f6' : '#ffffff';
            @endphp
            <tr style="background:{{ $bgRow }}; border-bottom:1px solid #e8e0e3;">
                <td style="padding:6px 10px; color:#1a1214; font-size:8.5px;">{{ $mov['fecha'] ?? '' }}</td>
                <td style="padding:6px 10px; color:#1a1214; font-size:8.5px;">{{ $mov['comprobante'] ?? '' }}</td>
                <td style="padding:6px 10px; color:#1a1214; font-size:8.5px;">{{ $mov['descripcion'] ?? '' }}</td>
                <td style="padding:6px 10px; color:#1a1214; font-size:8.5px;">{{ $mov['cuota'] ?? '' }}</td>
                <td style="padding:6px 10px; text-align:right; font-family:'DejaVu Sans Mono',monospace; font-size:8px; {{ $esPago ? 'color:#15803d; font-weight:600;' : 'color:#6b1a2a;' }}">
                    {{ $mov['importe'] ?? '' }}
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" style="padding:7px 10px; background:#f7f0f2; border-top:2px solid #6b1a2a; font-weight:700; font-size:8.5px; color:#4a1020; text-align:right; letter-spacing:0.5px;">Subtotal del período:</td>
            <td style="padding:7px 10px; background:#f7f0f2; border-top:2px solid #6b1a2a; font-weight:700; font-size:9px; color:#4a1020; text-align:right; font-family:'DejaVu Sans Mono',monospace;">$ {{ $consumo_actual ?? '0.00' }}</td>
        </tr>
        </tfoot>
    </table>
@endif

{{-- ══════════════════════════════════ 5. RESUMEN DEL PERÍODO ══════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
    <tr>
        <td style="font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:2.5px; color:#6b1a2a; white-space:nowrap; padding-right:12px; vertical-align:middle; width:1%;">Resumen del Período</td>
        <td style="border-bottom:1px solid #e8e0e3; vertical-align:middle;"></td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse; margin-bottom:16px;">
    <tr>
        <td style="width:25%; padding-right:7px; vertical-align:top;">
            <div style="border:1px solid #e8e0e3; border-left:3px solid #c9a96e; background:#ffffff; padding:9px 12px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1px; color:#8a7880; font-weight:600; margin-bottom:5px;">Gastos de Resumen</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:12px; font-weight:600; color:#6b1a2a;">$ {{ $gastos_resumen ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; padding-right:7px; vertical-align:top;">
            <div style="border:1px solid #e8e0e3; border-left:3px solid #c9a96e; background:#ffffff; padding:9px 12px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1px; color:#8a7880; font-weight:600; margin-bottom:5px;">Sellado</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:12px; font-weight:600; color:#6b1a2a;">$ {{ $sellado ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; padding-right:7px; vertical-align:top;">
            <div style="border:1px solid #e8e0e3; border-left:3px solid #c9a96e; background:#ffffff; padding:9px 12px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1px; color:#8a7880; font-weight:600; margin-bottom:5px;">Saldo Actual</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:12px; font-weight:600; color:#6b1a2a;">$ {{ $saldo_actual ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; vertical-align:top;">
            <div style="border:1px solid #e8e0e3; border-left:3px solid #c9a96e; background:#ffffff; padding:9px 12px;">
                <div style="font-size:6.5px; text-transform:uppercase; letter-spacing:1px; color:#8a7880; font-weight:600; margin-bottom:5px;">Consumo Actual</div>
                <div style="font-family:'DejaVu Sans Mono',monospace; font-size:12px; font-weight:600; color:#6b1a2a;">$ {{ $consumo_actual ?? '0.00' }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════ 6. AVISO ══════════════════════════════════ --}}
<div style="background:#f0fdf4; border:1px solid #bbf7d0; border-left:3px solid #16a34a; padding:8px 13px; margin-bottom:16px; font-size:7.5px; color:#166534; line-height:1.7;">
    <span style="font-weight:700; font-size:10px; margin-right:5px;">&#10003;</span>
    Este resumen se considera <strong>ACEPTADO</strong> si no se presenta reclamo
    dentro de los 30 días de su recepción, de conformidad con la Ley 25.065 de
    Tarjetas de Crédito y la normativa vigente del Banco Central de la República Argentina.
</div>

{{-- ══════════════════════════════════ 7. CUPÓN DE PAGO ══════════════════════════════════ --}}
<hr style="border:none; border-top:1.5px dashed #c8bcc0; margin:16px 0 12px;">
<div style="font-size:7px; font-weight:700; letter-spacing:3px; text-transform:uppercase; color:#8a7880; text-align:center; margin-bottom:10px;">✂ Cupón de Pago</div>

<table style="width:100%; border-collapse:collapse; font-size:8.5px; margin-bottom:10px;">
    <thead>
    <tr>
        <th style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left; width:28%;">Número Tarjeta</th>
        <th style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left; width:44%;">Titular</th>
        <th style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left; width:28%;">N° Resumen</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td style="border:1px solid #e8e0e3; padding:8px 10px;"><span style="font-family:'DejaVu Sans Mono',monospace; font-size:10px; font-weight:600; color:#6b1a2a;">{{ $nro_tarjeta ?? '—' }}</span></td>
        <td style="border:1px solid #e8e0e3; padding:8px 10px;"><span style="font-family:'DejaVu Sans Mono',monospace; font-size:10px; font-weight:600; color:#6b1a2a;">{{ $nombre_completo ?? '—' }}</span></td>
        <td style="border:1px solid #e8e0e3; padding:8px 10px;"><span style="font-family:'DejaVu Sans Mono',monospace; font-size:10px; font-weight:600; color:#6b1a2a;">{{ $nro_socio ?? '—' }}</span></td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="2" style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left;">Saldo Actual</th>
        <th style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left;">Pago Mínimo</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="2" style="border:1px solid #e8e0e3; padding:9px 10px; background:#4a1020;">
            <span style="font-size:6px; text-transform:uppercase; letter-spacing:1px; color:rgba(201,169,110,0.75); display:block; margin-bottom:3px;">Saldo Actual</span>
            <span style="font-family:'DejaVu Sans Mono',monospace; font-size:14px; font-weight:600; color:#ffffff;">$ {{ $saldo_actual ?? '0.00' }}</span>
        </td>
        <td style="border:1px solid #e8e0e3; padding:9px 10px; background:#4a3840;">
            <span style="font-size:6px; text-transform:uppercase; letter-spacing:1px; color:rgba(201,169,110,0.75); display:block; margin-bottom:3px;">Pago Mínimo</span>
            <span style="font-family:'DejaVu Sans Mono',monospace; font-size:14px; font-weight:600; color:#ffffff;">$ {{ $pago_minimo ?? '0.00' }}</span>
        </td>
    </tr>
    </tbody>
    <thead>
    <tr>
        <th colspan="3" style="background:#f7f0f2; border:1px solid #e8e0e3; padding:5px 9px; font-size:6.5px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:#4a3840; text-align:left;">Importe Abonado</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="3" style="border:1px solid #e8e0e3; padding:8px 10px; background:#ffffff; height:30px;">&nbsp;</td>
    </tr>
    </tbody>
</table>

<div style="font-size:8px; color:#4a3840; line-height:2.5; letter-spacing:0.3px;">
    SON PESOS: <span style="display:inline-block; width:180px; border-bottom:1px solid #4a3840;">&nbsp;</span>
    &nbsp;&nbsp;&nbsp;&nbsp; FECHA: ___/___/___
    &nbsp;&nbsp;&nbsp;&nbsp; FIRMA: <span style="display:inline-block; width:140px; border-bottom:1px solid #4a3840;">&nbsp;</span>
</div>

{{-- ══════════════════════════════════ 8. PIE ══════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin:16px 0 10px;">
    <tr>
        <td style="text-align:center; padding:9px 14px; background:#fdf5f6; border:1px solid #e8e0e3; border-left:4px solid #c9a96e;">
            <p style="font-size:9.5px; font-weight:700; color:#6b1a2a; letter-spacing:1.5px; text-transform:uppercase; margin:0;">
                PAGOS DE 8 a 13:00 hs &nbsp;·&nbsp; 17 a 18:30 hs
            </p>
        </td>
    </tr>
</table>
<hr style="border:none; border-top:1px solid #e8e0e3; margin-bottom:8px;">
<div style="font-size:6.5px; color:#c8bcc0; text-align:center; line-height:1.8;">
    Ley 25.065 — Régimen de Tarjetas de Crédito y Compra. Tasa Nominal Anual (TNA) y Costo Financiero Total (CFT)
    informados al momento de la acreditación. Las tasas pueden variar según las condiciones pactadas.
    Ante consultas o reclamos comunicarse con la Mutual al número habitual o presentarse en la sede social.
</div>
<table style="width:100%; border-collapse:collapse; margin-top:6px;">
    <tr>
        <td style="text-align:left; font-size:6.5px; color:#c8bcc0;">MUTUAL DE AMIGOS — CLUB SARMIENTO</td>
        <td style="text-align:right; font-size:6.5px; color:#c8bcc0;">Resumen N° {{ $nro_socio ?? '—' }} &nbsp;|&nbsp; {{ $fecha_periodo ?? '' }}</td>
    </tr>
</table>

</body>
</html>
