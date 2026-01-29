<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleta extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'branch_id',
        'client_id',
        'daily_summary_id',
        'tipo_documento',
        'serie',
        'correlativo',
        'numero_completo',
        'fecha_emision',
        'fecha_vencimiento',
        'ubl_version',
        'tipo_operacion',
        'moneda',
        'metodo_envio',
        // Forma de pago (igual que factura)
        'forma_pago_tipo',
        'forma_pago_cuotas',
        // Montos (igual que factura)
        'valor_venta',
        'mto_oper_gravadas',
        'mto_oper_exoneradas',
        'mto_oper_inafectas',
        'mto_oper_exportacion',
        'mto_oper_gratuitas',
        'mto_igv_gratuitas',
        'mto_igv',
        'mto_base_ivap',
        'mto_ivap',
        'mto_isc',
        'mto_icbper',
        'mto_otros_tributos',
        'mto_detraccion',
        'mto_percepcion',
        'mto_retencion',
        'total_impuestos',
        'sub_total',
        'mto_imp_venta',
        'mto_anticipos',
        'mto_descuentos',
        'descuento_global',
        // Detalles y configuraciones (igual que factura)
        'detalles',
        'leyendas',
        'guias',
        'documentos_relacionados',
        'detraccion',
        'percepcion',
        'retencion',
        'datos_adicionales',
        // Archivos generados
        'xml_path',
        'cdr_path',
        'pdf_path',
        // Estado SUNAT
        'estado_sunat',
        'respuesta_sunat',
        'codigo_hash',
        'usuario_creacion',
        // Consulta CPE
        'consulta_cpe_estado',
        'consulta_cpe_respuesta',
        'consulta_cpe_fecha',
        // Bancarización - Ley N° 28194
        'bancarizacion_aplica',
        'bancarizacion_monto_umbral',
        'bancarizacion_medio_pago',
        'bancarizacion_numero_operacion',
        'bancarizacion_fecha_pago',
        'bancarizacion_banco',
        'bancarizacion_validado',
        'bancarizacion_observaciones',
        // Múltiples medios de pago
        'medios_pago',
        // Anulación local
        'anulada_localmente',
        'motivo_anulacion_local',
        'observaciones_anulacion',
        'fecha_anulacion_local',
        'usuario_anulacion_id',
        // Anulación oficial (mediante resumen diario)
        'estado_anulacion',
        'motivo_anulacion',
        'fecha_solicitud_anulacion',
        'usuario_solicitud_anulacion_id',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'fecha_vencimiento' => 'date',
        // Forma de pago
        'forma_pago_cuotas' => 'array',
        // Montos (igual que factura)
        'valor_venta' => 'decimal:2',
        'mto_oper_gravadas' => 'decimal:2',
        'mto_oper_exoneradas' => 'decimal:2',
        'mto_oper_inafectas' => 'decimal:2',
        'mto_oper_exportacion' => 'decimal:2',
        'mto_oper_gratuitas' => 'decimal:2',
        'mto_igv_gratuitas' => 'decimal:2',
        'mto_igv' => 'decimal:2',
        'mto_base_ivap' => 'decimal:2',
        'mto_ivap' => 'decimal:2',
        'mto_isc' => 'decimal:2',
        'mto_icbper' => 'decimal:2',
        'mto_otros_tributos' => 'decimal:2',
        'mto_detraccion' => 'decimal:2',
        'mto_percepcion' => 'decimal:2',
        'mto_retencion' => 'decimal:2',
        'total_impuestos' => 'decimal:2',
        'sub_total' => 'decimal:2',
        'mto_imp_venta' => 'decimal:2',
        'mto_anticipos' => 'decimal:2',
        'mto_descuentos' => 'decimal:2',
        'descuento_global' => 'decimal:2',
        // Detalles y configuraciones (igual que factura)
        'detalles' => 'array',
        'leyendas' => 'array',
        'guias' => 'array',
        'documentos_relacionados' => 'array',
        'detraccion' => 'array',
        'percepcion' => 'array',
        'retencion' => 'array',
        'datos_adicionales' => 'array',
        // Consulta CPE
        'consulta_cpe_respuesta' => 'array',
        'consulta_cpe_fecha' => 'datetime',
        // Bancarización casts
        'bancarizacion_aplica' => 'boolean',
        'bancarizacion_monto_umbral' => 'decimal:2',
        'bancarizacion_fecha_pago' => 'date',
        'bancarizacion_validado' => 'boolean',
        // Múltiples medios de pago
        'medios_pago' => 'array',
        // Anulación local casts
        'anulada_localmente' => 'boolean',
        'fecha_anulacion_local' => 'datetime',
        // Anulación oficial casts
        'fecha_solicitud_anulacion' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function dailySummary(): BelongsTo
    {
        return $this->belongsTo(DailySummary::class);
    }

    public function getTipoDocumentoNameAttribute(): string
    {
        return 'Boleta de Venta Electrónica';
    }

    public function getEstadoSunatColorAttribute(): string
    {
        return match($this->estado_sunat) {
            'PENDIENTE' => 'warning',
            'ENVIADO' => 'info',
            'ACEPTADO' => 'success',
            'RECHAZADO' => 'danger',
            default => 'secondary'
        };
    }

    public function scopePending($query)
    {
        return $query->where('estado_sunat', 'PENDIENTE');
    }

    public function scopeAccepted($query)
    {
        return $query->where('estado_sunat', 'ACEPTADO');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('fecha_emision', [$startDate, $endDate]);
    }

    public function scopeForSummary($query)
    {
        return $query->where('metodo_envio', '!=', 'individual');
    }

    public function scopeIndividual($query)
    {
        return $query->where('metodo_envio', 'individual');
    }

    public function scopeWithoutSummary($query)
    {
        return $query->where(function($q) {
            $q->whereNull('daily_summary_id')
              ->orWhere('daily_summary_id', '');
        });
    }

    public function scopeNoAnuladaLocalmente($query)
    {
        return $query->where('anulada_localmente', false);
    }

    public function scopeAnuladaLocalmente($query)
    {
        return $query->where('anulada_localmente', true);
    }

    // Scopes para anulación oficial
    public function scopeSinAnular($query)
    {
        return $query->where('estado_anulacion', 'sin_anular');
    }

    public function scopePendienteAnulacion($query)
    {
        return $query->where('estado_anulacion', 'pendiente_anulacion');
    }

    public function scopeAnulada($query)
    {
        return $query->where('estado_anulacion', 'anulada');
    }

    // Scope para boletas que pueden ser incluidas en resumen de emisión (no anuladas ni pendientes de anulación)
    public function scopeDisponibleParaEmision($query)
    {
        return $query->whereIn('estado_anulacion', ['sin_anular']);
    }

    // Scope para boletas que pueden ser anuladas (aceptadas y no anuladas aún)
    public function scopeAnulable($query)
    {
        return $query->where('estado_sunat', 'ACEPTADO')
                    ->where('estado_anulacion', 'sin_anular')
                    ->where('anulada_localmente', false);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($boleta) {
            if (empty($boleta->numero_completo)) {
                $boleta->numero_completo = $boleta->serie . '-' . $boleta->correlativo;
            }
        });
    }
}