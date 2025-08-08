<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documento PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { border-bottom: 1px solid #ddd; margin-bottom: 10px; padding-bottom: 10px; }
        .totales { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #eee; padding: 6px; text-align: left; }
        th { background: #f8f8f8; }
    </style>
</head>
<body>
<div class="header">
    <h2>{{ $titulo ?? 'Documento' }}</h2>
    <div>Cliente: {{ $cliente->nombre ?? '' }}</div>
    <div>Fecha: {{ $fecha ?? now()->format('d/m/Y') }}</div>
</div>
<table>
    <thead><tr><th>Descripción</th><th>Cant.</th><th>Precio</th><th>IVA %</th><th>Subtotal</th></tr></thead>
    <tbody>
    @foreach($lineas as $l)
        <tr>
            <td>{{ $l->descripcion }}</td>
            <td>{{ number_format($l->cantidad, 2, ',', '.') }}</td>
            <td>{{ number_format($l->precio_unitario, 2, ',', '.') }}</td>
            <td>{{ number_format($l->iva_porcentaje, 2, ',', '.') }}</td>
            <td>{{ number_format($l->subtotal, 2, ',', '.') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="totales">
    <strong>Total: {{ number_format($total ?? 0, 2, ',', '.') }} €</strong>
</div>
</body>
</html>
