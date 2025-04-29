<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas por Día</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
        }

        .container {
            width: 80%;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .date-range {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 1.1em;
            color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Reporte de Ventas por Día</h1>

    <div class="date-range">
        <p><strong>Fecha de Inicio:</strong> {{ $fechaInicio->format('d-m-Y H:i') }}</p>
        <p><strong>Fecha de Fin:</strong> {{ $fechaFin->format('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre de Cerveza</th>
                <th>Cantidad Consumida (ml)</th>
                <th>Total Consumido (Valor)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ventas as $venta)
                <tr>
                    <td>{{ $venta->cerveza->nombre }}</td>
                    <td style="text-align: right">{{ number_format($venta->total_mililitros, 2) }} ml</td>
                    <td style="text-align: right">${{ number_format($venta->total_venta, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td><strong>Total</strong></td>
                <td style="text-align: right"><strong>{{ number_format($totalMililitros, 2) }} ml</strong></td>
                <td style="text-align: right"><strong>${{ number_format($totalValor, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Reporte generado el {{ now()->format('d-m-Y H:i') }}</p>
    </div>
</div>

</body>
</html>
