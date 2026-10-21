<div class="card shadow-sm">
    <div class="bg-success border-bottom d-flex justify-content-center align-items-center">
        <h4 class="mt-2 text-white">
             Top 5 Clientes del Año
        </h4>
    </div>  
    <div class="card-body table-responsive p-3">
        <table class="table table-hover table-bordered table-sm text-center bordered-rounded">
            <thead class="">
                <tr class="text-dark">
                    <th>Nombre</th>
                    <th>Cant. Compras</th>
                    <th>Total</th>
                    <th>Puntaje</th> <!-- Nueva columna -->
                </tr>
            </thead>
            <tbody>
                @foreach ($topClientes as $cliente)
                    <tr class="table-light">
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->total_compras }}</td>
                        <td>${{ number_format($cliente->monto_total, 2) }}</td>
                        <td>{{ number_format($cliente->score, 2) }}</td> <!-- Mostrar el puntaje -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<style>
    .bordered-rounded {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px; /* Ajusta el radio según tu preferencia */
        overflow: hidden; /* Asegura que el contenido quede dentro de los bordes redondeados */
    }

    .bordered-rounded th,
    .bordered-rounded td {
        border: 1px solid #dee2e6; /* Asegúrate de que coincida con el color del borde de Bootstrap */
    }

    .bordered-rounded tbody tr:last-child td {
        border-bottom-left-radius: 8px;
        border-bottom-right-radius: 8px;
    }

    .bordered-rounded thead tr:first-child th {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
</style>
