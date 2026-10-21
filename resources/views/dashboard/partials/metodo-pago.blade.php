
@php
    // Array de colores para las barras de progreso
    $colores = [ 'progress-light','progress-info', 'progress-secondary', 'progress-success', 'progress-primary', 'progress-dark'];
@endphp

<div class="card widget-todo">
    <div class="bg-dark border-bottom d-flex justify-content-center align-items-center">
        <h3 class=" text-white fs-5 mt-2">
             Met. Pago {{ $mesmetodopago }}
        </h3>
    </div>    
    <div class="card-body px-0 py-1">
        <table class='table table-borderless'>
            @foreach($ventasPorMetodoPago as $index => $venta)
            @php
                // Calcula el porcentaje de ventas para cada método
                $porcentaje = ($venta->total_ventas / $ventasPorMetodoPago->sum('total_ventas')) * 100;
                // Selecciona un color del array según el índice
                $color = $colores[$index % count($colores)];
            @endphp
            <tr>
                <td class='col-3'>{{ $venta->nombre }}</td>
                <td class='col-6'>
                    <div class="progress {{ $color }}">
                        <div class="progress-bar" role="progressbar" style="width: {{ $porcentaje }}%"
                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </td>
                <td class='col-3 text-center'>{{ number_format($porcentaje, 2) }}%</td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
