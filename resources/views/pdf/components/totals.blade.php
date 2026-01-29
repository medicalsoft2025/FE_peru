{{-- PDF Totals Component --}}
{{-- Props: $document, $format, $leyendas (optional) --}}

@if(in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4 Totals --}}
    <div class="totals-section">
        <div class="totals-left">
            @if(isset($leyendas) && !empty($leyendas))
                <div class="additional-info">
                    <div class="section-title">INFORMACIÓN ADICIONAL</div>
                    <div class="content">
                        @foreach($leyendas as $leyenda)
                            <p><strong>{{ $leyenda['codigo'] ?? '' }}:</strong> {{ $leyenda['descripcion'] ?? '' }}</p>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="totals-right">
            <table class="totals-table">
                @if($document->mto_oper_gravadas > 0)
                    <tr>
                        <td class="label">Operaciones Gravadas:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_oper_gravadas, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_oper_exoneradas > 0)
                    <tr>
                        <td class="label">Operaciones Exoneradas:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_oper_exoneradas, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_oper_inafectas > 0)
                    <tr>
                        <td class="label">Operaciones Inafectas:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_oper_inafectas, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_oper_exportacion > 0)
                    <tr>
                        <td class="label">Operaciones de Exportación:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_oper_exportacion, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_oper_gratuitas > 0)
                    <tr>
                        <td class="label">Operaciones Gratuitas:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_oper_gratuitas, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_igv > 0)
                    <tr>
                        <td class="label">IGV (18%):</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_igv, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_isc > 0)
                    <tr>
                        <td class="label">ISC:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_isc, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_icbper > 0)
                    <tr>
                        <td class="label">ICBPER:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_icbper, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_otros_tributos > 0)
                    <tr>
                        <td class="label">Otros Tributos:</td>
                        <td class="value">{{ $document->moneda }} {{ number_format($document->mto_otros_tributos, 2) }}</td>
                    </tr>
                @endif
                
                @if($document->mto_anticipos > 0)
                    <tr>
                        <td class="label">Descuento Anticipos:</td>
                        <td class="value">-{{ $document->moneda }} {{ number_format($document->mto_anticipos, 2) }}</td>
                    </tr>
                @endif

                @php
                    $descuentoGlobalA4 = $document->descuento_global ?? 0;
                    $totalDescuentosA4 = $document->mto_descuentos ?? 0;
                    $descuentoPorItemsA4 = $totalDescuentosA4 - $descuentoGlobalA4;
                @endphp
                @if($descuentoPorItemsA4 > 0)
                    <tr>
                        <td class="label" style="color: #c00;">Dcto. por Ítems:</td>
                        <td class="value" style="color: #c00;">-{{ $document->moneda }} {{ number_format($descuentoPorItemsA4, 2) }}</td>
                    </tr>
                @endif
                @if($descuentoGlobalA4 > 0)
                    <tr>
                        <td class="label" style="color: #c00;">Dcto. Global:</td>
                        <td class="value" style="color: #c00;">-{{ $document->moneda }} {{ number_format($descuentoGlobalA4, 2) }}</td>
                    </tr>
                @endif
                @if($totalDescuentosA4 > 0)
                    <tr>
                        <td class="label">Total Descuentos:</td>
                        <td class="value" style="color: #c00;">-{{ $document->moneda }} {{ number_format($totalDescuentosA4, 2) }}</td>
                    </tr>
                @endif

                <tr class="total-final">
                    <td class="label">TOTAL:</td>
                    <td class="value">{{ $document->moneda }} {{ number_format($document->mto_imp_venta, 2) }}</td>
                </tr>

                {{-- Medios de Pago --}}
                @if(!empty($document->medios_pago) && is_array($document->medios_pago))
                    <tr>
                        <td colspan="2" style="padding-top: 10px; border-top: 1px solid #ddd;">
                            <strong>MEDIOS DE PAGO:</strong>
                        </td>
                    </tr>
                    @foreach($document->medios_pago as $pago)
                        <tr>
                            <td class="label">{{ $pago['descripcion'] ?? $pago['tipo'] }}{{ !empty($pago['referencia']) ? ' (Ref: '.$pago['referencia'].')' : '' }}:</td>
                            <td class="value">{{ $document->moneda }} {{ number_format($pago['monto'], 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            </table>
        </div>
    </div>
@else
    {{-- Ticket Totals - NUEVO DISEÑO SIMPLE --}}
    <div class="totals-section">
        @if($document->mto_oper_gravadas > 0)
            <div class="total-line">Gravadas....{{ number_format($document->mto_oper_gravadas, 2) }}</div>
        @endif

        @if($document->mto_oper_inafectas > 0)
            <div class="total-line">Inafectas...{{ number_format($document->mto_oper_inafectas, 2) }}</div>
        @endif

        @if($document->mto_oper_exoneradas > 0)
            <div class="total-line">Exoneradas..{{ number_format($document->mto_oper_exoneradas, 2) }}</div>
        @endif

        @if($document->mto_oper_gratuitas > 0)
            <div class="total-line">Gratuitas...{{ number_format($document->mto_oper_gratuitas, 2) }}</div>
        @endif

        @php
            $descuentoGlobalTk = $document->descuento_global ?? 0;
            $totalDescuentosTk = $document->mto_descuentos ?? 0;
            $descuentoPorItemsTk = $totalDescuentosTk - $descuentoGlobalTk;
        @endphp
        @if($descuentoPorItemsTk > 0)
            <div class="total-line" style="color: #c00;">Dcto.Ítems..-{{ number_format($descuentoPorItemsTk, 2) }}</div>
        @endif
        @if($descuentoGlobalTk > 0)
            <div class="total-line" style="color: #c00;">Dcto.Global.-{{ number_format($descuentoGlobalTk, 2) }}</div>
        @endif
        @if($totalDescuentosTk > 0)
            <div class="total-line">Tot.Dctos...-{{ number_format($totalDescuentosTk, 2) }}</div>
        @endif

        @if($document->mto_igv > 0)
            <div class="total-line">IGV.........{{ number_format($document->mto_igv, 2) }}</div>
        @endif

        @if(($document->mto_isc ?? 0) > 0)
            <div class="total-line">ISC.........{{ number_format($document->mto_isc, 2) }}</div>
        @endif

        @if($document->mto_icbper > 0)
            <div class="total-line">ICBPER......{{ number_format($document->mto_icbper, 2) }}</div>
        @endif

        <div class="total-line total-final">TOTAL {{ $document->moneda }}...{{ number_format($document->mto_imp_venta, 2) }}</div>

        {{-- Medios de Pago en formato ticket --}}
        @if(!empty($document->medios_pago) && is_array($document->medios_pago))
            <div style="margin-top: 5px; padding-top: 5px; border-top: 1px dashed #000; font-size: 8px;">
                <strong>MEDIOS DE PAGO:</strong>
                @foreach($document->medios_pago as $pago)
                    <div>{{ $pago['descripcion'] ?? $pago['tipo'] }}: {{ number_format($pago['monto'], 2) }}{{ !empty($pago['referencia']) ? ' (Ref:'.$pago['referencia'].')' : '' }}</div>
                @endforeach
            </div>
        @endif
    </div>
@endif