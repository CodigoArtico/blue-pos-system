<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura</title>
    <style>
        /* ... (Tus estilos existentes) ... */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f5f5f5;
        }

        .header {
            background-color: #2c3e50;
            padding: 15px;
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            color: #ecf0f1;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header .number-box {
            position: absolute;
            top: 17px;
            left: 20px;
            border: 1px solid #ecf0f1;
            padding: 5px;
            color: #ecf0f1;
            font-weight: bold;
            background-color: #34495e;
        }

        .header .logo {
            position: absolute;
            top: 6px;
            right:10px;
            font-size: 28px;
            font-weight: bold;
            color: #ecf0f1;
        }

        /* ... (Continúa con tus estilos existentes) ... */
        .content {
            padding: 25px;
            position: relative;
            height: 150px;
            border: 1px solid #ddd;
            background-color: #fff;
            margin-top: 5px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .client-info,
        .company-info {
            margin-top: 0;
            width: 45%;
            font-size: 14px;
            color: #34495e;
        }

        .client-info h4,
        .company-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .client-info {
            position: absolute;
            left: 20px;
        }

        .company-info {
            position: absolute;
            right: 20px;
        }

        .divider {
            position: absolute;
            top: 10px;
            bottom: 10px;
            left: 50%;
            width: 1px;
            background-color: #ccc;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .table th {
            background-color: #34495e;
            color: #ecf0f1;
        }

        .table tr:nth-child(even) {
            background-color: #f7f9fb;
        }

        .summary {
            text-align: right;
            margin-top: 20px;
            font-size: 18px;
            color: #2c3e50;
        }

        .summary p {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .summary h3 {
            font-size: 24px;
            color: #e74c3c;
        }

        .payment-section {
            position: relative;
            padding: 15px;
            margin-top: 20px;
            height: 150px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .payment-info {
            position: absolute;
            left: 20px;
            width: 45%;
            font-size: 14px;
            color: #34495e;
        }

        .payment-info h4 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .payment-qr {
            position: absolute;
            right: 20px;
            width: 45%;
            text-align: center;
            font-size: 12px;
            color: #34495e;
        }

        .payment-qr h6 {
            margin-top: 2px;
            /* Ajusta este valor según el espacio que necesites */
            margin-bottom: 2px;
            /* Ajusta este valor también */
        }

        .payment-qr img {
            width: 120px;
            height: 120px;
            margin-bottom: 2px;
        }

        .footer {
            background-color: #2c3e50;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            color: #ecf0f1;
            margin-top: 20px;
            font-size: 16px;
            letter-spacing: 1px;
        }

        /* estilo de imagnes de estado  */
        .state-image {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: auto;
            z-index: 9999;
            /* Esto asegura que la imagen se muestre por encima de otros elementos */
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        {{ strtoupper($tipoComprobante) }}
        <div class="number-box">Nº: {{ $venta->serie }} -
            {{ str_pad($venta->correlativo ?? $venta->numero_comprobante, 4, '0', STR_PAD_LEFT) }}</div>
        <div class="logo">
            @if (!empty($logoPath) && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" style="width:152px; height:auto;">
            @else
                <!-- Logo por defecto o texto alternativo -->
                <img src="{{ asset('images/banner2.webp') }}" alt="Logo por defecto" style="width:140px; height:auto;">
            @endif
        </div>

        <!-- Agregar la fecha aquí -->
        <div class="invoice-date" style="margin-top: 0px; font-size: 16px; color: #ffffff;">
            <strong>F. Emision:</strong> {{ \Carbon\Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i') }}
        </div>
    </div>

    <!-- Client and Company Information Box -->
    <div class="content">
        <div class="client-info">
            <h4>DATOS DEL CLIENTE</h4>
            <p>
                <strong>RAZON S:</strong> {{ $venta->cliente->persona->razon_social }}<br>
                <strong>NUM DOC:</strong> {{ $venta->cliente->persona->numero_documento }}<br>
                <strong>TIP PER:</strong> {{ strtoupper($venta->cliente->persona->tipo_persona) }}<br>
                <strong>DIRECC:</strong> {{ $venta->cliente->persona->direccion }}
            </p>
        </div>
        <div class="divider"></div>
        <div class="company-info">
            <h4>DATOS DE LA EMPRESA</h4>
            <p>
                <strong>RAZON S:</strong> {{ $empresa->razon_social_comprobante }}<br>
                <strong>RUC:</strong> {{ $empresa->ruc_dni }}<br>
                <strong>DIRECC:</strong> {{ $empresa->direccion_comprobante }}<br>
                <strong>CEL:</strong> {{ $empresa->telefono_comprobante }}
            </p>
        </div>
    </div>
    {{-- imagenes de estado  --}}
    @if ($venta->comprobante->tipo_comprobante == 'BOLETA' || $venta->comprobante->tipo_comprobante == 'FACTURA')
        @if ($venta->estado == 1 && !isset($venta->emisionComprobante))
            <img src="{{ public_path('images/failed-logo.png') }}" alt="Logo por defecto" class="state-image">
        @elseif($venta->estado == 0)
            <img src="{{ public_path('images/canceled-logo.png') }}" alt="Logo por defecto" class="state-image">
        @endif
    @elseif($venta->comprobante->tipo_comprobante == 'NOTA VENTA')
        @if ($venta->estado == 0)
            <img src="{{ public_path('images/canceled-logo.png') }}" alt="Logo por defecto" class="state-image">
        @endif
    @endif


    <!-- Table -->
    <table class="table">
        <thead>
            <tr>
                <th>Detalle</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($venta->productos as $producto)
                <tr>
                    <td>{{ $producto->nombre }}</td>
                    <td>{{ $producto->pivot->cantidad }}</td>
                    <td>S/ {{ number_format($producto->pivot->precio_ventas, 2) }}</td>
                    <td>S/ {{ number_format($producto->pivot->cantidad * $producto->pivot->precio_ventas, 2) }}</td>
                </tr>
            @endforeach

            @foreach ($venta->productosLibres as $productoLibre)
                <tr>
                    <td>{{ $productoLibre->nombre }}</td>
                    <td>{{ $productoLibre->cantidad }}</td>
                    <td>S/ {{ number_format($productoLibre->precio_venta, 2) }}</td>
                    <td>S/ {{ number_format($productoLibre->cantidad * $productoLibre->precio_venta, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary -->
    <div class="summary">
        <p>DESCUENTO: S/ {{ number_format($totalDescuentos, 2) }}</p>
        <p>OP. SUBTOTAL: S/ {{ number_format($subTotalNormal + $subTotalLibre - $totalDescuentos, 2) }}</p>
        <p>IGV 0%: S/ {{ number_format($impuesto, 2) }}</p>
        @php
            $total = $subTotalNormal + $subTotalLibre - $totalDescuentos + $impuesto;
        @endphp

        <h3>TOTAL: S/ {{ number_format($total, 2) }}</h3>
        <p>SON: {{ $importeTotalEnLetras }}</p>
    </div>

    <!-- Payment Section with QR -->
    <div class="payment-section">
        <div class="payment-info">
            <h4>INFORMACIÓN DE PAGO</h4>
            <p>
                {{-- {{ ucfirst($venta->metodoPago->nombre) }}<br> --}}
                Metodo Pago: {{ $venta->metodoPago->nombre ?? 'Banco Borcelle' }}<br>
                {{-- Nombre: {{ $empresa->nombre_contacto ?? 'Alba Castro' }}<br>
                Número de cuenta: {{ $empresa->numero_cuenta ?? '123-456-789' }} --}}
                @if ($numeroOperacion)
                    <p>
                        Número de Operación: {{ $numeroOperacion }}
                    </p>
                @endif
                @if ($montoRecibido)
                    <p>
                        Monto Recibido: {{ $montoRecibido }} Vuelto: {{ $montoRecibido - $total }}
                    </p>
                @endif
            </p>
        </div>
        <div class="payment-qr">
            @if ($tempQRPath)
                <img src="{{ public_path('storage/qr_temp.png') }}" alt="QR">
                <h6>Representación de {{ $tipoComprobante }} ELECTRONICA</h6>
                <h6> {{ $venta->emisionComprobante->hash_cpe ?? 'Hash no disponible' }}</h6>
            @else
                <!-- Opcional: Mensaje o imagen alternativa si no hay QR -->
            @endif
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        codart.deviper.net </div>

</body>

</html>
