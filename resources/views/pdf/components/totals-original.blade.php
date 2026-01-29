{{-- PDF Totals Component (Original Style) --}}
{{-- Props: $document, $format, $qr_code, $hash, $fecha_emision, $total_en_letras, $totales --}}

@if(in_array($format, ['a4', 'A4', 'a5', 'A5']))
    <table style="width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #000; border-radius: 3px; overflow: hidden; font-size: 7px;">
        <tr>
            {{-- Columna izquierda: Solo QR --}}
            <td style="width: 22%; padding: 3px; vertical-align: middle; text-align: center; border-right: 1px solid #000; border-bottom: 1px solid #000;">
                @if(isset($qr_code) && $qr_code)
                    <img src="{{ $qr_code }}" alt="QR" style="width: 100%; height: auto; max-width: 120px; display: block; margin: 0 auto;">
                @endif
            </td>

            {{-- Columna central: Fecha + Pago + Leyendas (ajustable) --}}
            <td style="padding: 3px; vertical-align: top; border-right: 1px solid #000; border-bottom: 1px solid #000; font-size: 7px; line-height: 1.4;">
                {{-- Fecha y Pago primero --}}
                <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd;">
                    <div style="margin-bottom: 2px;"><b>FECHA EMISIÓN:</b> {{ $fecha_emision }}</div>
                    <div><b>CONDICIÓN DE PAGO:</b> {{ strtoupper($document->forma_pago_tipo ?? 'CONTADO') }}</div>
                </div>

                {{-- Observaciones --}}
                @if(!empty($document->observaciones))
                    <div style="margin-bottom: 3px; padding-bottom: 3px; border-bottom: 1px solid #ddd; font-size: 6px;">
                        <b>OBSERVACIONES:</b><br>{{ $document->observaciones }}
                    </div>
                @endif

                {{-- Leyendas sin el texto "NÚMERO EN LETRAS" --}}
                @if(!empty($document->leyendas))
                    @php
                        $leyendas = is_array($document->leyendas) ? $document->leyendas : json_decode($document->leyendas, true);
                        $leyendas = $leyendas ?? [];
                    @endphp
                    @foreach($leyendas as $leyenda)
                        @php
                            $leyendaValue = $leyenda['value'] ?? '';
                            // Filtrar la leyenda que contiene "NÚMERO EN LETRAS"
                            if (stripos($leyendaValue, 'NÚMERO EN LETRAS') !== false ||
                                stripos($leyendaValue, 'NUMERO EN LETRAS') !== false) {
                                continue;
                            }
                        @endphp
                        <div style="margin-bottom: 2px; font-size: 6px; line-height: 1.3;">• {{ $leyendaValue }}</div>
                    @endforeach
                @endif

                {{-- Hash --}}
                @if($hash)
                    <div style="margin-top: 3px; padding-top: 3px; border-top: 1px solid #ddd; font-size: 5px;">
                        <b>HASH:</b> {{ substr($hash, 0, 30) }}...
                    </div>
                @endif
            </td>

            {{-- Columna derecha: Totales (ancho fijo) --}}
            <td style="width: 30%; padding: 0; vertical-align: top; border-bottom: 1px solid #000;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total Ope. Gravadas</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ $totales['subtotal_formatted'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total Ope. Inafectadas</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ number_format($document->mto_oper_inafectas ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total Ope. Exoneradas</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ number_format($document->mto_oper_exoneradas ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total Ope. Gratuitas</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ number_format($document->mto_oper_gratuitas ?? 0, 2) }}</td>
                    </tr>
                    @php
                        $descuentoGlobal = $document->descuento_global ?? 0;
                        $totalDescuentos = $document->mto_descuentos ?? 0;
                        $descuentoPorItems = $totalDescuentos - $descuentoGlobal;
                    @endphp
                    @if($descuentoPorItems > 0)
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px; color: #c00;">Dcto. por Ítems</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap; color: #c00;">-{{ $totales['moneda'] }} {{ number_format($descuentoPorItems, 2) }}</td>
                    </tr>
                    @endif
                    @if($descuentoGlobal > 0)
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px; color: #c00;">Dcto. Global</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap; color: #c00;">-{{ $totales['moneda'] }} {{ number_format($descuentoGlobal, 2) }}</td>
                    </tr>
                    @endif
                    @if($totalDescuentos > 0)
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total Descuentos</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">-{{ $totales['moneda'] }} {{ number_format($totalDescuentos, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total IGV</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ $totales['igv_formatted'] }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 7px;">Total ISC</td>
                        <td style="padding: 2px 4px; text-align: right; border-bottom: 1px solid #000; font-size: 7px; white-space: nowrap;">{{ $totales['moneda'] }} {{ number_format($document->mto_isc ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 2px 4px; text-align: right; font-weight: bold; background: #f0f0f0; font-size: 8px;">TOTAL A PAGAR</td>
                        <td style="padding: 2px 4px; text-align: right; background: #f0f0f0; font-weight: bold; font-size: 8px; white-space: nowrap;">{{ $totales['moneda'] }} {{ $totales['total_formatted'] }}</td>
                    </tr>
                    {{-- Medios de Pago en formato A4/A5 --}}
                    @if(!empty($document->medios_pago) && is_array($document->medios_pago))
                        <tr>
                            <td colspan="2" style="padding: 4px 4px 2px; border-top: 1px solid #000;">
                                <strong style="font-size: 7px;">MEDIOS DE PAGO:</strong>
                            </td>
                        </tr>
                        @foreach($document->medios_pago as $pago)
                            <tr>
                                <td style="padding: 1px 4px; text-align: right; font-size: 6px;">{{ $pago['descripcion'] ?? $pago['tipo'] }}{{ !empty($pago['referencia']) ? ' (Ref:'.$pago['referencia'].')' : '' }}</td>
                                <td style="padding: 1px 4px; text-align: right; font-size: 6px; white-space: nowrap;">{{ $totales['moneda'] }} {{ number_format($pago['monto'], 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </table>
            </td>
        </tr>
    </table>
@else
    {{-- Diseño para Tickets (58mm/80mm) - BLANCO Y NEGRO --}}
    <div style="margin-top: 5px; padding-top: 5px; border-top: 2px solid #000; font-family: 'Helvetica', 'Arial', sans-serif;">

        {{-- TOTALES --}}
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; font-size: 8px;">
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total Ope. Gravadas</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ $totales['subtotal_formatted'] ?? '0.00' }}</td>
            </tr>
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total Ope. Inafectadas</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($document->mto_oper_inafectas ?? 0, 2) }}</td>
            </tr>
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total Ope. Exoneradas</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($document->mto_oper_exoneradas ?? 0, 2) }}</td>
            </tr>
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total Ope. Gratuitas</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($document->mto_oper_gratuitas ?? 0, 2) }}</td>
            </tr>
            @php
                $descuentoGlobalTicket = $document->descuento_global ?? 0;
                $totalDescuentosTicket = $document->mto_descuentos ?? 0;
                $descuentoPorItemsTicket = $totalDescuentosTicket - $descuentoGlobalTicket;
            @endphp
            @if($descuentoPorItemsTicket > 0)
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold; color: #c00;">Dcto. por Ítems</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal; color: #c00;">-{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($descuentoPorItemsTicket, 2) }}</td>
            </tr>
            @endif
            @if($descuentoGlobalTicket > 0)
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold; color: #c00;">Dcto. Global</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal; color: #c00;">-{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($descuentoGlobalTicket, 2) }}</td>
            </tr>
            @endif
            @if($totalDescuentosTicket > 0)
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total Descuentos</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">-{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($totalDescuentosTicket, 2) }}</td>
            </tr>
            @endif
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total IGV</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ $totales['igv_formatted'] ?? '0.00' }}</td>
            </tr>
            <tr style="border-bottom: 1px dashed #000;">
                <td style="padding: 2px 0; text-align: left; font-weight: bold;">Total ISC</td>
                <td style="padding: 2px 0; text-align: right; font-weight: normal;">{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($document->mto_isc ?? 0, 2) }}</td>
            </tr>
            <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                <td style="padding: 4px 0; text-align: left; font-weight: bold; font-size: 9px;">TOTAL A PAGAR</td>
                <td style="padding: 4px 0; text-align: right; font-weight: bold; font-size: 9px;">{{ $totales['moneda'] ?? 'PEN' }} {{ $totales['total_formatted'] ?? '0.00' }}</td>
            </tr>
            {{-- Medios de Pago en formato ticket --}}
            @if(!empty($document->medios_pago) && is_array($document->medios_pago))
                <tr>
                    <td colspan="2" style="padding: 4px 0; border-top: 1px dashed #000;">
                        <strong style="font-size: 8px;">MEDIOS DE PAGO:</strong>
                    </td>
                </tr>
                @foreach($document->medios_pago as $pago)
                    <tr>
                        <td style="padding: 2px 0; text-align: left; font-size: 7px;">{{ $pago['descripcion'] ?? $pago['tipo'] }}{{ !empty($pago['referencia']) ? ' (Ref:'.$pago['referencia'].')' : '' }}</td>
                        <td style="padding: 2px 0; text-align: right; font-size: 7px;">{{ $totales['moneda'] ?? 'PEN' }} {{ number_format($pago['monto'], 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </table>

        {{-- TOTAL EN LETRAS --}}
        @if(isset($total_en_letras) && $total_en_letras)
            <div style="margin: 5px 0; padding: 3px; border: 1px solid #000; font-size: 7px; text-align: center; font-weight: bold;">
                SON: {{ strtoupper($total_en_letras) }}
            </div>
        @endif

        {{-- CÓDIGO QR --}}
        @if(isset($qr_code) && $qr_code)
            <div style="text-align: center; margin: 8px 0; padding: 5px 0; border-top: 1px dashed #000; border-bottom: 1px dashed #000;">
                <img src="{{ $qr_code }}" alt="QR Code" style="width: {{ $format === '80mm' ? '110px' : '85px' }}; height: {{ $format === '80mm' ? '110px' : '85px' }}; display: block; margin: 0 auto;">

                {{-- HASH DEBAJO DEL QR --}}
                @if(isset($hash) && $hash)
                    <div style="margin-top: 3px; font-size: 6px; text-align: center; word-break: break-all; font-family: 'Helvetica', 'Arial', sans-serif;">
                        <strong style="display: block; margin-bottom: 2px;">CÓDIGO HASH:</strong>
                        <span style="background-color: #f0f0f0; padding: 2px 3px; border: 1px solid #000; display: inline-block;">{{ $hash }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- REPRESENTACIÓN IMPRESA --}}
        <div style="text-align: center; font-size: 7px; margin-top: 5px; line-height: 1.3;">
            Representación impresa del Comprobante Electrónico<br>
            Puede verificarla en www.sunat.gob.pe
        </div>

        {{-- OBSERVACIONES --}}
        @if(!empty($document->observaciones))
            <div style="margin-top: 5px; padding: 3px; border: 1px dashed #000; font-size: 7px;">
                <strong>OBSERVACIONES:</strong> {{ $document->observaciones }}
            </div>
        @endif

        {{-- CONDICIÓN DE PAGO --}}
        <div style="margin-top: 3px; font-size: 7px; text-align: center;">
            <strong>CONDICIÓN DE PAGO:</strong> {{ strtoupper($document->forma_pago_tipo ?? 'CONTADO') }}
        </div>
    </div>
@endif