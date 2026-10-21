<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f5f5f5;
        }

        /* Asegurarse de que la página sea horizontal en la impresión */
        @page {
            size: landscape;
            margin: 20mm;
        }

        /* Header */
        .header {
            background-color: #2c3e50;
            padding: 20px;
            position: relative;
            color: #ecf0f1;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Título */
        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }

        /* Logo */
        .header .logo {
            position: absolute;
            right: 20px;
            top: 5px;
        }

        .header .logo img {
            max-width: 120px;
            height: auto;
        }

        /* Tabla sin bordes */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            table-layout: fixed;
            overflow-x: auto;
        }

        /* Remover bordes de las celdas, y hacer que las celdas tengan un espacio adecuado */
        th,
        td {
            padding: 12px 15px;
            text-align: center;
            font-size: 14px;
        }

        /* Añadir un borde solo a las cabeceras de la tabla para que sea más legible */
        th {
            background-color: #34495e;
            color: #ecf0f1;
            font-size: 16px;
            font-weight: bold;
        }

        td {
            font-size: 14px;
            color: #34495e;
        }

        tr:nth-child(even) {
            background-color: #f7f9fb;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* Estilo para las etiquetas de estado */
        .badge {
            padding: 5px;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .bg-warning {
            background-color: #ffc107;
            color: #fff;
        }

        .bg-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .bg-primary {
            background-color: #007bff;
            color: #fff;
        }

        .bg-success {
            background-color: #28a745;
            color: #fff;
        }

        .bg-light {
            background-color: #f8f9fa;
            color: #212529;
        }

        .category {
            font-size: 0.9em;
            margin-right: 5px;
            background-color: #34495e;
            color: #fff;
            padding: 3px 8px;
            border-radius: 5px;
        }

        /* Estilo para la cabecera de la tabla cuando está en modo landscape */
        .table-container {
            overflow-x: auto;
            white-space: nowrap;
        }
    </style>
</head>

<body>

    <!-- Header con logo a la derecha y título alineado -->
    <div class="header">
        <h1>Reporte de Productos Vendidos</h1>
        <div class="logo">
            <img src="{{ $logoPath }}" alt="Logo">
        </div>
    </div>

    <!-- Fechas del reporte -->
    <div style="margin-top: 20px; text-align: center;">
        <strong>Fecha de inicio:</strong> {{ $startDate }} <br>
        <strong>Fecha de fin:</strong> {{ $endDate }}
    </div>

    <!-- Contenedor de tabla con desplazamiento horizontal -->
    <div class="table-container">
        <!-- Tabla con productos -->
        <table>
            <thead>
                <tr>
                    {{-- <th>Código</th> --}}
                    <th>Nombre</th>
                    <th>Marca</th>
                    {{-- <th>Presentación</th> --}}
                    {{-- <th>Categorías</th> --}}
                    <th>Cant. Vend</th>
                    <th>Total Vent</th>
                    <th>Pre. Comp</th>
                    <th>Pre. Vent</th>
                    <th>Margen Ganan.</th>
                    <th>% Ganan.</th>
                    <th>T. Ganan.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $item)
                    @php
                        $ventas = $ventas_por_producto[$item->id] ?? 0;
                        if ($ventas >= 1) {
                            $precioCompra = $item->precioCompraReciente();
                            $precioVenta = $item->precioVentaReciente();
                            $margen = $precioVenta - $precioCompra;
                            $porcentajeGanancia = $precioCompra > 0 ? ($margen / $precioCompra) * 100 : 0;
                            $totalVentas = $ventas * $precioVenta;
                    @endphp
                        <tr>
                            
                            <td>
                                @if ($item->stock >= 1 && $item->stock <= 5 && $item->estado == 1)
                                    {{ $item->nombre }} <span class="badge bg-warning">{{ $item->stock }}</span>
                                @elseif ($item->stock == 0 && $item->estado == 1)
                                    {{ $item->nombre }} <span class="badge bg-danger">{{ $item->stock }}</span>
                                @else
                                    {{ $item->nombre }} <span class="badge bg-primary">{{ $item->stock }}</span>
                                @endif
                            </td>
                            <td>{{ $item->marca->caracteristica->nombre ?? 'N/A' }}</td>
                            
                            <td>{{ $ventas }}</td>
                            <td>S/. {{ number_format($totalVentas, 2) }}</td>
                            <td>S/. {{ number_format($precioCompra, 2) }}</td>
                            <td>S/. {{ number_format($precioVenta, 2) }}</td>
                            <td>S/. {{ number_format($margen, 2) }}</td>
                            <td>{{ number_format($porcentajeGanancia, 2) }}%</td>
                            <td>{{ number_format($ventas * $margen, 2) }}</td>
                            <td>
                                @if ($item->estado == 1)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-light">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                    @php
                        }
                    @endphp
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
