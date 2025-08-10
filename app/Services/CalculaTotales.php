<?php

namespace App\Services;

use Illuminate\Support\Collection;

// === Reemplaza tu función por esta:
protected static function recalcTotalsFromPlaceholders(array $lineas, callable $set): void
{
    $base = 0.0;
    $dtoTotal = 0.0;
    $iva = 0.0;
    $irpf = 0.0;

    foreach ($lineas as $l) {
        $cantidad = (float) ($l['cantidad'] ?? ($l['cantidad_placeholder'] ?? 0));
        $precio   = (float) ($l['precio_unitario'] ?? ($l['precio_unitario_placeholder'] ?? 0));
        $dtoPct   = (float) ($l['descuento_porcentaje'] ?? ($l['descuento_porcentaje_placeholder'] ?? 0));
        $ivaPct   = (float) ($l['iva_porcentaje'] ?? ($l['iva_porcentaje_placeholder'] ?? 0));
        $irpfPct  = (float) ($l['irpf_porcentaje'] ?? 0);

        $sub = round($cantidad * $precio, 2);
        $dtoImporte = round($sub * $dtoPct / 100, 2);
        $subConDto = $sub - $dtoImporte;

        $base += $subConDto;
        $dtoTotal += $dtoImporte;
        $iva += round($subConDto * $ivaPct / 100, 2);
        $irpf += round($subConDto * $irpfPct / 100, 2);
    }

    $base = round($base, 2);
    $dtoTotal = round($dtoTotal, 2);
    $iva = round($iva, 2);
    $irpf = round($irpf, 2);
    $total = round($base + $iva - $irpf, 2);

    $set('base_imponible', number_format($base, 2, '.', ''));
    $set('descuento_total', number_format($dtoTotal, 2, '.', ''));
    $set('iva_total', number_format($iva, 2, '.', ''));
    $set('irpf_total', number_format($irpf, 2, '.', ''));
    $set('total', number_format($total, 2, '.', ''));
}
