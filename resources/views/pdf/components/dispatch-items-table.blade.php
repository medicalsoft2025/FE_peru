{{-- PDF Dispatch Items Table Component --}}
{{-- Props: $detalles, $format --}}
@php
    $maxFilas = in_array($format, ['a5', 'A5']) ? 8 : 12;
    $contador = count($detalles);

    // Calcular peso total de todos los items
    $pesoTotalItems = 0;
    foreach ($detalles as $det) {
        $pesoTotalItems += $det['peso_total'] ?? $det['peso_bruto'] ?? $det['peso_unitario'] ?? $det['peso'] ?? 0;
    }
@endphp

@if(in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4/A5 Dispatch Items Table --}}
    <div style="margin: 8px 0;">
        <div style="background-color: #f0f0f0; padding: 4px 8px; border: 1px solid #000; border-bottom: none; border-radius: 3px 3px 0 0;">
            <h3 style="margin: 0; font-size: 10px; font-weight: bold;">DETALLE DE BIENES A TRASLADAR</h3>
        </div>
        <table class="items-table" style="border-radius: 0 0 3px 3px;">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">Nº</th>
                    <th style="width: 15%; text-align: center;">CÓDIGO</th>
                    <th style="width: 45%; text-align: left;">DESCRIPCIÓN</th>
                    <th style="width: 10%; text-align: center;">UNIDAD</th>
                    <th style="width: 12%; text-align: right;">CANTIDAD</th>
                    <th style="width: 13%; text-align: right;">PESO (KG)</th>
                </tr>
            </thead>
            <tbody>
                {{-- Items reales --}}
                @foreach($detalles as $index => $detalle)
                    @php
                        // El peso puede venir como 'peso_total', 'peso_bruto', 'peso_unitario' o 'peso'
                        $pesoItem = $detalle['peso_total'] ?? $detalle['peso_bruto'] ?? $detalle['peso_unitario'] ?? $detalle['peso'] ?? 0;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="text-align: center;">{{ $detalle['codigo'] ?? '' }}</td>
                        <td style="text-align: left;">{{ $detalle['descripcion'] ?? '' }}</td>
                        <td style="text-align: center;">{{ $detalle['unidad'] ?? 'NIU' }}</td>
                        <td style="text-align: right;">{{ number_format($detalle['cantidad'] ?? 0, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($pesoItem, 3) }}</td>
                    </tr>
                @endforeach

                {{-- Filas vacías --}}
                @for($i = $contador; $i < $maxFilas; $i++)
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <td colspan="4" style="text-align: right; padding: 4px 8px; border-top: 1px solid #000;">TOTALES:</td>
                    <td style="text-align: right; padding: 4px 8px; border-top: 1px solid #000;">{{ count($detalles) }} ítem(s)</td>
                    <td style="text-align: right; padding: 4px 8px; border-top: 1px solid #000;">{{ number_format($pesoTotalItems, 3) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    {{-- Ticket Dispatch Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="col-codigo">Cód.</th>
                <th class="col-descripcion">Descripción</th>
                <th class="col-cantidad">Cant.</th>
                <th class="col-peso">Peso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($detalles as $detalle)
                @php
                    $pesoItemTicket = $detalle['peso_total'] ?? $detalle['peso_bruto'] ?? $detalle['peso_unitario'] ?? $detalle['peso'] ?? 0;
                @endphp
                <tr>
                    <td class="text-center">{{ $detalle['codigo'] ?? '-' }}</td>
                    <td class="text-left">{{ Str::limit($detalle['descripcion'] ?? '', 20) }}</td>
                    <td class="text-center">{{ number_format($detalle['cantidad'] ?? 0, 2) }}</td>
                    <td class="text-right">{{ number_format($pesoItemTicket, 3) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif