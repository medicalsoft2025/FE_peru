{{-- PDF Dispatch Client Info Component --}}
{{-- Props: $destinatario, $format, $fecha_emision, $fecha_traslado, $peso_total_formatted --}}

@if(in_array($format, ['a4', 'A4', 'a5', 'A5']))
    {{-- A4/A5 Destinatario Info --}}
    <div style="margin: 8px 0; border: 1px solid #000; border-radius: 3px;">
        <div style="background-color: #f0f0f0; padding: 4px 8px; border-bottom: 1px solid #000;">
            <h3 style="margin: 0; font-size: 10px; font-weight: bold;">DATOS DEL DESTINATARIO</h3>
        </div>
        <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
            <tr>
                <td style="padding: 4px 8px; width: 15%; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                    <strong>{{ ($destinatario->tipo_documento ?? '6') == '6' ? 'RUC' : 'DNI' }}:</strong>
                </td>
                <td style="padding: 4px 8px; width: 35%; border-bottom: 1px solid #ccc;">
                    {{ $destinatario->numero_documento ?? 'N/A' }}
                </td>
                <td style="padding: 4px 8px; width: 15%; border-left: 1px solid #ccc; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                    <strong>F. EMISIÓN:</strong>
                </td>
                <td style="padding: 4px 8px; width: 35%; border-bottom: 1px solid #ccc;">
                    {{ $fecha_emision }}
                </td>
            </tr>
            <tr>
                <td style="padding: 4px 8px; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                    <strong>DESTINATARIO:</strong>
                </td>
                <td style="padding: 4px 8px; border-bottom: 1px solid #ccc;">
                    {{ $destinatario->razon_social ?? 'DESTINATARIO' }}
                </td>
                <td style="padding: 4px 8px; border-left: 1px solid #ccc; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;">
                    <strong>F. TRASLADO:</strong>
                </td>
                <td style="padding: 4px 8px; border-bottom: 1px solid #ccc;">
                    {{ $fecha_traslado }}
                </td>
            </tr>
            @if(isset($destinatario->direccion) && $destinatario->direccion)
            <tr>
                <td style="padding: 4px 8px; border-right: 1px solid #ccc;">
                    <strong>DIRECCIÓN:</strong>
                </td>
                <td colspan="3" style="padding: 4px 8px;">
                    {{ $destinatario->direccion }}
                </td>
            </tr>
            @endif
        </table>
    </div>
@else
    {{-- Ticket Destinatario Info --}}
    <div class="client-section">
        <div class="client-row">
            <span class="client-label">DESTINATARIO:</span> {{ strtoupper($destinatario->razon_social ?? 'DESTINATARIO') }}
        </div>
        
        @if(isset($destinatario->numero_documento))
            <div class="client-row">
                <span class="client-label">{{ ($destinatario->tipo_documento ?? '6') == '6' ? 'RUC' : 'DNI' }}:</span> {{ $destinatario->numero_documento }}
            </div>
        @endif
        
        @if(isset($destinatario->direccion) && $destinatario->direccion)
            <div class="client-row break-word">
                <span class="client-label">DIR:</span> {{ $destinatario->direccion }}
            </div>
        @endif
        
        <div class="client-row">
            <span class="client-label">F.EMISIÓN:</span> {{ $fecha_emision }}
        </div>
        
        <div class="client-row">
            <span class="client-label">F.TRASLADO:</span> {{ $fecha_traslado }}
        </div>
    </div>
@endif