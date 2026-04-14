<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Resumen {{ $nro_socio ?? '' }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10px;
        color: #222;
        background: #fff;
        padding: 22px 26px;
    }

    /* ══════════════════════════════════════════
       HEADER
    ══════════════════════════════════════════ */
    .header-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .header-logo-cell {
        width: 38%;
        vertical-align: middle;
        padding-right: 12px;
    }
    .header-info-cell {
        width: 62%;
        text-align: right;
        vertical-align: middle;
    }
    .header-title {
        font-size: 28px;
        font-weight: bold;
        color: #1a1a2e;
        letter-spacing: 4px;
        line-height: 1;
    }
    .header-sub {
        font-size: 8.5px;
        color: #666;
        margin-top: 5px;
        line-height: 1.8;
        letter-spacing: 0.3px;
    }
    .header-sub strong {
        color: #1a1a2e;
        font-weight: bold;
    }
    .header-divider {
        border: none;
        border-top: 3px solid #1a1a2e;
        margin-top: 12px;
        margin-bottom: 14px;
    }

    /* ══════════════════════════════════════════
       SECCIÓN CLIENTE
    ══════════════════════════════════════════ */
    .cliente-box {
        background: #f9f9f7;
        border-left: 5px solid #1a1a2e;
        padding: 10px 14px;
        margin-bottom: 14px;
    }
    .cliente-table { width: 100%; border-collapse: collapse; }
    .cliente-info-cell { vertical-align: top; }
    .cliente-nombre {
        font-size: 14px;
        font-weight: bold;
        color: #1a1a2e;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .cliente-dato {
        font-size: 8.5px;
        color: #777;
        line-height: 1.7;
    }
    .cliente-badge-cell {
        width: 130px;
        text-align: right;
        vertical-align: middle;
    }
    .badge-label {
        font-size: 7px;
        color: #aaa;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 3px;
    }
    .badge-socio {
        display: inline-block;
        background: #1a1a2e;
        color: #fff;
        font-size: 8px;
        font-weight: bold;
        padding: 5px 10px;
        letter-spacing: 0.8px;
    }

    /* ══════════════════════════════════════════
       CARDS DE TOTALES
    ══════════════════════════════════════════ */
    .cards-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 4px;
    }
    .card-cell { width: 33.33%; padding: 0 5px; vertical-align: top; }
    .card-cell:first-child { padding-left: 0; }
    .card-cell:last-child  { padding-right: 0; }

    .card {
        border: 1px solid #e0e0e0;
        padding: 11px 14px;
        background: #fff;
    }
    .card-highlight {
        background: #1a1a2e;
        padding: 11px 14px;
    }
    .card-label {
        font-size: 7.5px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #aaa;
        margin-bottom: 6px;
        font-weight: bold;
    }
    .card-highlight .card-label { color: #8899bb; }
    .card-value {
        font-family: 'DejaVu Sans Mono', monospace;
        font-size: 14px;
        font-weight: bold;
        color: #1a1a2e;
    }
    .card-highlight .card-value { color: #fff; font-size: 15px; }

    /* fila secundaria debajo de las cards */
    .cards-meta-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        margin-top: 6px;
    }
    .cards-meta-cell { font-size: 8px; color: #bbb; padding: 0 0 0 2px; }
    .cards-meta-sep { font-size: 8px; color: #ddd; padding: 0 10px; }

    /* ══════════════════════════════════════════
       TABLA DE MOVIMIENTOS
    ══════════════════════════════════════════ */
    .section-title {
        font-size: 7.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #aaa;
        margin-bottom: 6px;
        border-bottom: 1px solid #eee;
        padding-bottom: 4px;
    }
    .mov-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        font-size: 9px;
    }
    .mov-table thead tr { background: #1a1a2e; }
    .mov-table thead th {
        padding: 7px 9px;
        text-align: left;
        font-size: 7.5px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #a0b0cc;
        font-weight: bold;
    }
    .mov-table thead th.col-right { text-align: right; }
    .mov-table tbody tr.row-odd  { background: #fff; }
    .mov-table tbody tr.row-even { background: #fafafa; }
    .mov-table tbody td {
        padding: 5px 9px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f0f0;
        color: #333;
    }
    .mov-table tbody td.col-right {
        text-align: right;
        font-family: 'DejaVu Sans Mono', monospace;
        font-size: 8.5px;
    }
    .importe-pago  { color: #15803d; font-weight: bold; }
    .importe-cargo { color: #1a1a2e; }

    .mov-tfoot-row { background: #f8f8f6; }
    .mov-tfoot-row td {
        padding: 6px 9px;
        font-weight: bold;
        font-size: 9px;
        border-top: 2px solid #1a1a2e;
        color: #1a1a2e;
    }

    /* ══════════════════════════════════════════
       RESUMEN DEL PERÍODO (4 columnas)
    ══════════════════════════════════════════ */
    .rf-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
    .rf-cell { width: 25%; padding: 0 4px; vertical-align: top; }
    .rf-cell:first-child { padding-left: 0; }
    .rf-cell:last-child  { padding-right: 0; }
    .rf-box {
        border: 1px solid #e0e0e0;
        padding: 8px 11px;
        background: #fafafa;
    }
    .rf-label {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #aaa;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .rf-value {
        font-family: 'DejaVu Sans Mono', monospace;
        font-size: 11px;
        font-weight: bold;
        color: #1a1a2e;
    }

    /* ══════════════════════════════════════════
       AVISO ACEPTADO
    ══════════════════════════════════════════ */
    .aviso-box {
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-left: 4px solid #16a34a;
        padding: 7px 12px;
        margin-bottom: 12px;
        font-size: 8px;
        color: #15803d;
        line-height: 1.6;
    }
    .aviso-check { font-weight: bold; font-size: 10px; margin-right: 5px; }

    /* ══════════════════════════════════════════
       CUPÓN DE PAGO
    ══════════════════════════════════════════ */
    .cupon-divider {
        border: none;
        border-top: 2px dashed #999;
        margin: 14px 0 12px 0;
    }
    .cupon-title {
        font-size: 8px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #888;
        margin-bottom: 8px;
        text-align: center;
    }
    .cupon-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 9px;
    }
    .cupon-table th {
        background: #f0f0f0;
        border: 1px solid #bbb;
        padding: 4px 8px;
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: bold;
        color: #555;
        text-align: left;
    }
    .cupon-table td {
        border: 1px solid #bbb;
        padding: 7px 9px;
        vertical-align: middle;
    }
    .cupon-td-dark {
        background: #1a1a2e;
    }
    .cupon-td-mid {
        background: #374151;
    }
    .cupon-td-blank {
        background: #fff;
    }
    .cupon-val-white {
        font-family: 'DejaVu Sans Mono', monospace;
        font-weight: bold;
        font-size: 12px;
        color: #fff;
    }
    .cupon-val-label-white {
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #8899bb;
        display: block;
        margin-bottom: 2px;
    }
    .cupon-val-dark {
        font-family: 'DejaVu Sans Mono', monospace;
        font-weight: bold;
        font-size: 11px;
        color: #1a1a2e;
    }
    .cupon-firma {
        font-size: 8px;
        color: #555;
        margin-top: 8px;
        line-height: 2.2;
    }
    .cupon-firma-line {
        border-bottom: 1px solid #555;
        display: inline-block;
        width: 180px;
    }

    /* ══════════════════════════════════════════
       PIE LEGAL
    ══════════════════════════════════════════ */
    .pie-divider {
        border: none;
        border-top: 1px solid #ddd;
        margin-bottom: 7px;
        margin-top: 4px;
    }
    .pie-texto {
        font-size: 7px;
        color: #bbb;
        line-height: 1.7;
        text-align: center;
    }
    .pie-footer-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    .pie-footer-left  { text-align: left;  font-size: 7px; color: #bbb; }
    .pie-footer-right { text-align: right; font-size: 7px; color: #bbb; }
</style>
</head>
<body>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 1. HEADER                                              --}}
{{-- ══════════════════════════════════════════════════════ --}}
<table class="header-table">
    <tr>
        <td class="header-logo-cell">
            @if(!empty($logoBase64))
                <div style="background:#1a1a2e; padding:8px 12px; border-radius:6px; display:inline-block;">
                    <img src="{{ $logoBase64 }}"
                         style="height:48px; width:auto; filter:brightness(0) invert(1);">
                </div>
            @else
                <div style="font-weight:700; font-size:13px; color:#1a1a2e; line-height:1.5; letter-spacing:0.5px;">
                    MUTUAL DE AMIGOS<br>CLUB SARMIENTO
                </div>
            @endif
        </td>
        <td class="header-info-cell">
            <div class="header-title">RESUMEN</div>
            <div class="header-sub">
                <strong>N°</strong> {{ $nro_socio ?? '—' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Período:</strong> {{ $fecha_periodo ?? '—' }}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <strong>Vencimiento:</strong> {{ $fecha_vencimiento ?? '—' }}
            </div>
        </td>
    </tr>
</table>
<hr class="header-divider">

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 2. SECCIÓN CLIENTE                                     --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="cliente-box">
    <table class="cliente-table">
        <tr>
            <td class="cliente-info-cell">
                <div class="cliente-nombre">{{ $nombre_completo ?? '—' }}</div>
                <div class="cliente-dato">
                    {{ $direccion ?? '' }}{{ !empty($localidad) ? ' — ' . $localidad : '' }}
                    @if(!empty($nro_tarjeta))
                        &nbsp;&nbsp;·&nbsp;&nbsp;N° Tarjeta: {{ $nro_tarjeta }}
                    @endif
                </div>
            </td>
            <td class="cliente-badge-cell">
                <div class="badge-label">N° Socio</div>
                <div class="badge-socio">{{ $nro_socio ?? '—' }}</div>
            </td>
        </tr>
    </table>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 3. CARDS DE TOTALES (4 columnas)                       --}}
{{-- ══════════════════════════════════════════════════════ --}}
<table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
    <tr>
        <td style="width:25%; padding-right:6px;">
            <div class="card-highlight">
                <div class="card-label">Saldo Actual</div>
                <div class="card-value">$ {{ $saldo_actual ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; padding:0 3px;">
            <div class="card">
                <div class="card-label">Pago Mínimo</div>
                <div class="card-value">$ {{ $pago_minimo ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; padding:0 3px;">
            <div class="card">
                <div class="card-label">Saldo Anterior</div>
                <div class="card-value">$ {{ $saldo_anterior ?? '0.00' }}</div>
            </div>
        </td>
        <td style="width:25%; padding-left:6px;">
            <div class="card">
                <div class="card-label">Vencimiento</div>
                <div class="card-value">{{ $fecha_vencimiento ?? '—' }}</div>
            </div>
        </td>
    </tr>
</table>

<table style="width:100%; border-collapse:collapse; margin-top:6px; margin-bottom:16px;">
    <tr>
        <td style="width:50%; padding:6px 10px; background:#f8f8f6; border:1px solid #e5e5e5;">
            <div style="font-size:8px; color:#999; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">
                Límite de compra
            </div>
            <div style="font-size:12px; font-weight:700; color:#555; font-family:'Courier New',monospace;">
                $ {{ $limite_compra ?? '0.00' }}
            </div>
        </td>
        <td style="width:16px;"></td>
        <td style="width:50%; padding:6px 10px; background:#f8f8f6; border:1px solid #e5e5e5;">
            <div style="font-size:8px; color:#999; text-transform:uppercase; letter-spacing:1px; margin-bottom:2px;">
                Consumo actual
            </div>
            <div style="font-size:12px; font-weight:700; color:#555; font-family:'Courier New',monospace;">
                $ {{ $consumo_actual ?? '0.00' }}
            </div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 4. TABLA DE MOVIMIENTOS                               --}}
{{-- ══════════════════════════════════════════════════════ --}}
@if(!empty($movimientos))
<div class="section-title">Movimientos del Período</div>
<table class="mov-table">
    <thead>
        <tr>
            <th style="width:13%">Fecha</th>
            <th style="width:14%">Comprobante</th>
            <th>Descripción</th>
            <th style="width:9%">Cuota</th>
            <th class="col-right" style="width:17%">Importe</th>
        </tr>
    </thead>
    <tbody>
        @php $i = 0; @endphp
        @foreach($movimientos as $mov)
            @php
                $i++;
                $esPago = str_starts_with(trim($mov['importe'] ?? ''), '-');
            @endphp
            <tr class="{{ $i % 2 === 0 ? 'row-even' : 'row-odd' }}">
                <td>{{ $mov['fecha'] ?? '' }}</td>
                <td>{{ $mov['comprobante'] ?? '' }}</td>
                <td>{{ $mov['descripcion'] ?? '' }}</td>
                <td>{{ $mov['cuota'] ?? '' }}</td>
                <td class="col-right {{ $esPago ? 'importe-pago' : 'importe-cargo' }}">
                    {{ $mov['importe'] ?? '' }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="mov-tfoot-row">
            <td colspan="4" style="text-align:right; letter-spacing:0.5px;">Subtotal del período:</td>
            <td style="text-align:right; font-family:'DejaVu Sans Mono',monospace; font-size:10px;">
                $ {{ $consumo_actual ?? '0.00' }}
            </td>
        </tr>
    </tfoot>
</table>
@endif

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 5. RESUMEN DEL PERÍODO (4 columnas)                    --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="section-title">Resumen del Período</div>
<table class="rf-table">
    <tr>
        <td class="rf-cell">
            <div class="rf-box">
                <div class="rf-label">Gastos de Resumen</div>
                <div class="rf-value">$ {{ $gastos_resumen ?? '0.00' }}</div>
            </div>
        </td>
        <td class="rf-cell">
            <div class="rf-box">
                <div class="rf-label">Sellado</div>
                <div class="rf-value">$ {{ $sellado ?? '0.00' }}</div>
            </div>
        </td>
        <td class="rf-cell">
            <div class="rf-box">
                <div class="rf-label">Saldo Actual</div>
                <div class="rf-value">$ {{ $saldo_actual ?? '0.00' }}</div>
            </div>
        </td>
        <td class="rf-cell">
            <div class="rf-box">
                <div class="rf-label">Consumo Actual</div>
                <div class="rf-value">$ {{ $consumo_actual ?? '0.00' }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 6. AVISO ACEPTADO                                      --}}
{{-- ══════════════════════════════════════════════════════ --}}
<div class="aviso-box">
    <span class="aviso-check">&#10003;</span>
    Este resumen se considera <strong>ACEPTADO</strong> si no se presenta reclamo
    dentro de los 30 días de su recepción, de conformidad con la Ley 25.065 de
    Tarjetas de Crédito y la normativa vigente del Banco Central de la República Argentina.
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 7. CUPÓN DE PAGO                                       --}}
{{-- ══════════════════════════════════════════════════════ --}}
<hr class="cupon-divider">
<div class="cupon-title">✂ Cupón de Pago</div>

<table class="cupon-table">
    <thead>
        <tr>
            <th style="width:28%">Número Tarjeta</th>
            <th style="width:44%">Titular</th>
            <th style="width:28%">N° Resumen</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><span class="cupon-val-dark">{{ $nro_tarjeta ?? '—' }}</span></td>
            <td><span class="cupon-val-dark">{{ $nombre_completo ?? '—' }}</span></td>
            <td><span class="cupon-val-dark">{{ $nro_socio ?? '—' }}</span></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="2">Saldo Actual</th>
            <th>Pago Mínimo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2" class="cupon-td-dark">
                <span class="cupon-val-label-white">Saldo Actual</span>
                <span class="cupon-val-white">$ {{ $saldo_actual ?? '0.00' }}</span>
            </td>
            <td class="cupon-td-mid">
                <span class="cupon-val-label-white">Pago Mínimo</span>
                <span class="cupon-val-white">$ {{ $pago_minimo ?? '0.00' }}</span>
            </td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="3">Importe Abonado</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3" class="cupon-td-blank" style="height:28px;">
                &nbsp;
            </td>
        </tr>
    </tbody>
</table>

<div class="cupon-firma">
    SON PESOS: <span class="cupon-firma-line">&nbsp;</span>
    &nbsp;&nbsp;&nbsp;&nbsp; FECHA: ___/___/___
    &nbsp;&nbsp;&nbsp;&nbsp; FIRMA: <span class="cupon-firma-line">&nbsp;</span>
</div>

{{-- ══════════════════════════════════════════════════════ --}}
{{-- 8. PIE LEGAL                                           --}}
{{-- ══════════════════════════════════════════════════════ --}}
<hr class="pie-divider">
<div class="pie-texto">
    Ley 25.065 — Régimen de Tarjetas de Crédito y Compra. Tasa Nominal Anual (TNA) y Costo Financiero Total (CFT)
    informados al momento de la acreditación. Las tasas pueden variar según las condiciones pactadas.
    Ante consultas o reclamos comunicarse con la Mutual al número habitual o presentarse en la sede social.
</div>
<table class="pie-footer-table">
    <tr>
        <td class="pie-footer-left">MUTUAL DE AMIGOS — CLUB SARMIENTO</td>
        <td class="pie-footer-right">
            Resumen N° {{ $nro_socio ?? '—' }} &nbsp;|&nbsp; {{ $fecha_periodo ?? '' }}
        </td>
    </tr>
</table>

</body>
</html>
