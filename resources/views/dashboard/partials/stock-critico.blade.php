{{-- toma css de top-clientes  --}}
<div class="card shadow-sm">
    <div class="bg-warning border-bottom d-flex justify-content-center align-items-center">
        <h4 class="mt-2 text-white">
             Prod - Stock Crítico
        </h4>
    </div>  
    <div class="card-body table-responsive p-3" style="max-height: 300px; overflow-y: auto;">
        <table class="table table-hover table-bordered table-sm text-center bordered-rounded">
            <thead class="">
                <tr class="text-dark">
                    <th>Nombre</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productosStockCritico as $producto)
                    <tr class="table-light">
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
