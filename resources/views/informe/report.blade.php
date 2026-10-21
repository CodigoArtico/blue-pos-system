<thead>
    <tr>
        <th colspan="13" style="text-align: center;">
            <strong>Fecha de inicio:</strong> {{ $startDate ?? 'No especificada' }} &nbsp;&nbsp;
            <strong>| Fecha de fin:</strong> {{ $endDate ?? 'No especificada' }}
        </th>
    </tr>
    <tr>
        <th>Código</th>
        <th>Nombre</th>
        <th>Marca</th>
        <th>Presentación</th>
        <th>Categorías</th>
        <th>Cant. Vendida</th>
        <th>Total de Ventas</th>
        <th>Precio de Compra</th>
        <th>Precio de Venta</th>
        <th>Margen de Ganancia</th>
        <th>Porcentaje de Ganancia</th>
        <th>Total Ganancia.</th>
        <th>Estado</th>
        {{-- <th>Acción</th> <!-- Si deseas agregar una columna de acción, puedes incluirla aquí --> --}}
    </tr>
</thead>
<tbody>
    @foreach ($productos as $item)
        @php
            // Solo mostrar productos con al menos 1 ventas
            $ventas = $ventas_por_producto[$item->id] ?? 0;
            if ($ventas >= 1) {
                $precioCompra = $item->precioCompraReciente();
                $precioVenta = $item->precioVentaReciente();
                $margen = $precioVenta - $precioCompra;
                $porcentajeGanancia = $precioCompra > 0 ? ($margen / $precioCompra) * 100 : 0;
                $totalVentas = $ventas * $precioVenta;
            }
        @endphp
        @if ($ventas >= 1) <!-- Solo mostrar productos con al menos 1 ventas -->
            <tr>
                <td>{{$item->codigo}}</td>
                <td>{{$item->nombre}}</td>
                <td>{{$item->marca->caracteristica->nombre ?? 'N/A'}}</td>
                <td>{{$item->presentacion->caracteristica->nombre ?? 'N/A'}}</td>
                <td>
                    @foreach ($item->categorias as $category)
                        <div class="container" style="font-size: small;">
                            <div class="row">
                                <span class="m-1 rounded-pill p-1 bg-secondary text-white text-center">{{$category->caracteristica->nombre ?? 'N/A'}}</span>
                            </div>
                        </div>
                    @endforeach
                </td>
                <td>{{$ventas}}</td>
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
                {{-- <td> <!-- Columna de acción -->
                    <!-- Puedes agregar un botón de acción aquí si lo necesitas -->
                    <button class="btn btn-info">Ver Detalles</button>
                </td> --}}
            </tr>
        @endif
    @endforeach
</tbody>
