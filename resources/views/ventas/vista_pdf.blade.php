<!-- resources/views/ventas/vista_pdf.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Venta</title>
    <style>
        /* Estilos para el PDF */
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-center {
            text-align: center; /* Clase para centrar el texto */
        }
        .text-right {
            text-align: right; /* Clase para alinear el texto a la derecha */
        }
    </style>
</head>
<body>
    <h1>Reporte de Venta #{{ $record->id }}</h1>
    <h2>Venta</h2>
    <p><strong>Fecha y Hora:</strong> {{ $record->created_at }}</p>
    <p><strong>Usuario (Registrado por):</strong> {{ $record->usuario->name }}</p>

    <p><strong>Cliente:</strong> {{ $record->cliente->nombres ?? null }}</p>
    <p><strong>Cédula del Cliente:</strong> {{ $record->cliente->cedula ?? null }}</p> {{-- aquí parece que estás mostrando el correo, podrías corregir si es cédula --}}
    <p><strong>Correo del Cliente:</strong> {{ $record->cliente->correo ?? null }}</p>

    <p><strong>Código Serial Pulsera:</strong> {{ $record->pulsera->codigo_serial }}</p>
    <p><strong>UID Pulsera:</strong> {{ $record->pulsera->codigo_uid }}</p>

    <h2>Detalles de la Venta</h2>
    <table>
        <thead>
            <tr>
                <th>Cerveza</th>
                <th>Mililitros Consumidos</th>
                <th>Precio por Mililitro</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($record->detalles as $detalle)
                <tr>
                    <td>{{ $detalle->cerveza->nombre }}</td>
                    <td class="text-right">{{ $detalle->mililitros_consumidos }}</td>
                    <td class="text-right">{{ $detalle->precio_por_mililitro }}</td>
                    <td class="text-right">${{ number_format($detalle->total, 2) }}</td>
                </tr>
            @endforeach


            @foreach ($record->detallesPromociones as $detalle)
                <tr>
                    <td colspan="3" class="text-center">{{ $detalle->descripcion_snapshot }}</td>
                    <td class="text-right" style="color: red">-${{ number_format($detalle->total_descuento, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-center"><strong> Total Pagar</strong> </td>
                <td class="text-right">${{number_format($record->total_pagar, 2)}}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
