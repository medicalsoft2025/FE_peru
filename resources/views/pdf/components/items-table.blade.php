{{-- PDF Items Table Component --}}
{{-- Props: $detalles, $format --}}
@php
    $maxFilas = in_array($format, ['a5', 'A5']) ? 8 : 15;
    $contador = count($detalles);

    // Verificar si hay descuentos en algún detalle
    // Puede venir como: 'descuentos' (array original), 'descuento' (valor calculado), o 'mto_descuento'
    $tieneDescuentos = false;
    foreach ($detalles as $detalle) {
        if (!empty($detalle['descuentos']) || ($detalle['descuento'] ?? 0) > 0 || ($detalle['mto_descuento'] ?? 0) > 0) {
            $tieneDescuentos = true;
            break;
        }
    }
@endphp

@if (in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4/A5 Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th>Nº</th>
                <th>CÓDIGO</th>
                <th>DESCRIPCIÓN</th>
                <th>UNIDAD</th>
                <th>CANT.</th>
                <th>P. UNIT.</th>
                @if($tieneDescuentos)
                    <th>DCTO.</th>
                @endif
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items reales --}}
            @foreach ($detalles as $index => $detalle)
                @php
                    $cantidad = $detalle['cantidad'] ?? 0;
                    $mtoValorUnitario = $detalle['mto_valor_unitario'] ?? 0;
                    $porcentajeIgv = $detalle['porcentaje_igv'] ?? 18;
                    $tipAfeIgv = $detalle['tip_afe_igv'] ?? '10';

                    // Calcular descuento del detalle
                    $descuentoLinea = 0;
                    $factorDescuento = 0;
                    if (!empty($detalle['descuentos'])) {
                        foreach ($detalle['descuentos'] as $descuento) {
                            $descuentoLinea += $descuento['monto'] ?? 0;
                            if (($descuento['factor'] ?? 0) > 0) {
                                $factorDescuento = $descuento['factor'];
                            }
                        }
                    } elseif (($detalle['descuento'] ?? 0) > 0) {
                        $descuentoLinea = $detalle['descuento'];
                    } elseif (($detalle['mto_descuento'] ?? 0) > 0) {
                        $descuentoLinea = $detalle['mto_descuento'];
                    }

                    // Calcular precio unitario ORIGINAL (sin descuento)
                    if ($descuentoLinea > 0 && $mtoValorUnitario > 0) {
                        // Si hay descuento, calcular precio unitario original con IGV
                        if (in_array($tipAfeIgv, ['10', '17'])) {
                            $precioUnitarioOriginal = $mtoValorUnitario * (1 + $porcentajeIgv / 100);
                        } else {
                            $precioUnitarioOriginal = $mtoValorUnitario;
                        }
                        $subtotalSinDescuento = $cantidad * $precioUnitarioOriginal;
                        $totalLinea = $subtotalSinDescuento - $descuentoLinea;
                    } else {
                        // Sin descuento, usar precio guardado
                        $precioUnitarioOriginal = $detalle['mto_precio_unitario'] ?? 0;
                        $subtotalSinDescuento = $cantidad * $precioUnitarioOriginal;
                        $totalLinea = $subtotalSinDescuento;
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detalle['codigo'] ?? '' }}</td>
                    <td>
                        {{ $detalle['descripcion'] ?? '' }}
                        @if($descuentoLinea > 0)
                            @if(!empty($detalle['descuentos']))
                                @foreach($detalle['descuentos'] as $desc)
                                    @if(($desc['factor'] ?? 0) > 0)
                                        <br><small style="color: #666; font-size: 9px;">Dcto. {{ number_format(($desc['factor'] ?? 0) * 100, 0) }}%</small>
                                    @endif
                                @endforeach
                            @elseif($factorDescuento > 0)
                                <br><small style="color: #666; font-size: 9px;">Dcto. {{ number_format($factorDescuento * 100, 0) }}%</small>
                            @endif
                        @endif
                    </td>
                    <td>{{ $detalle['unidad'] ?? 'NIU' }}</td>
                    <td>{{ number_format($cantidad, 2) }}</td>
                    <td>{{ number_format($precioUnitarioOriginal, 2) }}</td>
                    @if($tieneDescuentos)
                        <td style="color: #c00;">{{ $descuentoLinea > 0 ? '-' . number_format($descuentoLinea, 2) : '' }}</td>
                    @endif
                    <td>{{ number_format($totalLinea, 2) }}</td>
                </tr>
            @endforeach

            {{-- Filas vacías --}}
            @for ($i = $contador; $i < $maxFilas; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if($tieneDescuentos)
                        <td></td>
                    @endif
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>
@else
    {{-- Ticket Items (58mm/80mm) - NUEVO DISEÑO SIMPLE --}}
    <div class="items-section">
        <div style="border-bottom: 1px solid #000; padding-bottom: 2px; margin-bottom: 2px; font-weight: bold; font-size: {{ $format === '80mm' ? '11px' : '10px' }}; text-align: center;">
            --- PRODUCTOS ---
        </div>

        @foreach ($detalles as $index => $detalle)
            @php
                $cantidadTicket = $detalle['cantidad'] ?? 0;
                $mtoValorUnitarioTicket = $detalle['mto_valor_unitario'] ?? 0;
                $porcentajeIgvTicket = $detalle['porcentaje_igv'] ?? 18;
                $tipAfeIgvTicket = $detalle['tip_afe_igv'] ?? '10';

                // Calcular descuento del detalle
                $descuentoTicket = 0;
                $porcentajeDescuento = 0;
                if (!empty($detalle['descuentos'])) {
                    foreach ($detalle['descuentos'] as $descuento) {
                        $descuentoTicket += $descuento['monto'] ?? 0;
                        if (($descuento['factor'] ?? 0) > 0) {
                            $porcentajeDescuento = ($descuento['factor'] ?? 0) * 100;
                        }
                    }
                } elseif (($detalle['descuento'] ?? 0) > 0) {
                    $descuentoTicket = $detalle['descuento'];
                } elseif (($detalle['mto_descuento'] ?? 0) > 0) {
                    $descuentoTicket = $detalle['mto_descuento'];
                }

                // Calcular precio unitario ORIGINAL (sin descuento)
                if ($descuentoTicket > 0 && $mtoValorUnitarioTicket > 0) {
                    if (in_array($tipAfeIgvTicket, ['10', '17'])) {
                        $precioTicketOriginal = $mtoValorUnitarioTicket * (1 + $porcentajeIgvTicket / 100);
                    } else {
                        $precioTicketOriginal = $mtoValorUnitarioTicket;
                    }
                    $subtotalTicket = $cantidadTicket * $precioTicketOriginal;
                    $totalTicket = $subtotalTicket - $descuentoTicket;
                } else {
                    $precioTicketOriginal = $detalle['mto_precio_unitario'] ?? 0;
                    $subtotalTicket = $cantidadTicket * $precioTicketOriginal;
                    $totalTicket = $subtotalTicket;
                }
            @endphp
            <div style="margin-bottom: 3px; padding-bottom: 2px; border-bottom: 1px dashed #999; font-size: {{ $format === '80mm' ? '10px' : '9px' }};">
                {{-- Descripción --}}
                <div style="font-weight: bold; margin-bottom: 1px;">
                    {{ $detalle['descripcion'] ?? '' }}
                </div>

                {{-- Detalles en una línea --}}
                <div>
                    {{ number_format($cantidadTicket, 2) }} x {{ number_format($precioTicketOriginal, 2) }} = {{ number_format($subtotalTicket, 2) }}
                </div>

                {{-- Mostrar descuento si existe --}}
                @if($descuentoTicket > 0)
                    <div style="color: #c00; font-size: {{ $format === '80mm' ? '9px' : '8px' }};">
                        Dcto{{ $porcentajeDescuento > 0 ? ' ' . number_format($porcentajeDescuento, 0) . '%' : '' }}: -{{ number_format($descuentoTicket, 2) }}
                        <span style="float: right;">= {{ number_format($totalTicket, 2) }}</span>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif
