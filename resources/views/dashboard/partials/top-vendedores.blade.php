<div class="card">
    <div class="card-header">
        <h3 class="card-heading text-dark fs-5">TOP 3 VENDEDORES MES <button type="button" class="btn btn-dark p-0 text-white ms-5 py-1 px-1" data-bs-toggle="modal" data-bs-target="#topAnualModal">
            <i class="fa-solid fa-up-down-left-right"></i>
        </button></h3>
        
    </div>
    <div class="card-body">
        <!-- Contenedor con scroll horizontal -->
        <div class="user-list-container">
            @php
                // Calcular puntaje ponderado y ordenar el array en la vista
                $topUsuarios = $topUsuarios
                    ->map(function ($usuario) {
                        // Aquí definimos el puntaje ponderado
                        $usuario->puntaje = $usuario->total_ventas * 0.5 + $usuario->monto_ventas * 0.5;
                        return $usuario;
                    })
                    ->sortByDesc('puntaje')
                    ->values();
            @endphp

            @foreach ($topUsuarios as $index => $usuario)
                <div class="user-card">
                    <div class="card p-3 mb-4" style="border-radius: 20px; background-color: #5ed9ff;">
                        <div class="user-avatar">
                            <img src="{{ $usuario->img && file_exists(storage_path('app/public/profile_images/' . $usuario->img))
                                ? asset('storage/profile_images/' . $usuario->img)
                                : asset('images/profile1.png') }}"
                                class="rounded-circle" width="100" height="100" alt="Avatar">
                        </div>
                        <h4 class='mt-3 text-dark'>{{ $usuario->name }}</h4>
                        <p class='text-sm text-dark'>
                            Estado: {{ $usuario->estado == 1 ? 'Activo' : 'Inactivo' }}
                        </p>
                        <p class='text-sm text-dark'>Libres: No</p>
                        <h5 class='text-xl font-bold text-dark'>
                            Ventas: {{ $usuario->total_ventas }} | Monto:
                            ${{ number_format($usuario->monto_ventas, 2) }}
                        </h5>
                        <p class='text-sm text-dark'
                            title="Se basa en el número de ventas y el monto total de ventas con una ponderación del 50% cada uno">
                            Puntaje: {{ number_format($usuario->puntaje, 2) }}
                        </p>
                        <!-- Ventas mensuales del año actual para el usuario -->
                        <div class="mt-1">
                            <h6 class="text-lg font-semibold text-dark">Ventas Mensuales:</h6>
                            <ul class="list-unstyled text-dark">
                                @foreach ($ventasMensuales->get($usuario->id, collect()) as $venta)
                                    <li>{{ \Carbon\Carbon::create()->month($venta->mes)->format('F') }}:
                                        {{ $venta->cantidad_total }} ventas</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Efecto de borde animado -->
                        <div
                            class="ribbon {{ $index == 0 ? 'ribbon-gold' : ($index == 1 ? 'ribbon-silver' : 'ribbon-bronze') }}">
                            {{ $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal para mostrar el Top Anual -->
<div class="modal fade" id="topAnualModal" tabindex="-1" aria-labelledby="topAnualLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark text-center" id="topAnualLabel">Top 3 Vendedores Anual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row text-center">
                    @foreach ($topUsuariosAnual as $index => $usuario)
                        <div class="col-md-4 col-12">
                            <div class="card p-3 mb-4" style="border-radius: 20px; background-color: #5ed9ff;">
                                <div class="user-avatar">
                                    <img src="{{ $usuario->img && file_exists(storage_path('app/public/profile_images/' . $usuario->img))
                                        ? asset('storage/profile_images/' . $usuario->img)
                                        : asset('images/profile1.png') }}"
                                        class="rounded-circle" width="100" height="100" alt="Avatar">
                                </div>
                                <h4 class='mt-3 text-dark'>{{ $usuario->name }}</h4>
                                <h5 class='text-xl font-bold text-dark'>
                                    Ventas: {{ $usuario->total_ventas }} | Monto:
                                    ${{ number_format($usuario->monto_ventas, 2) }}
                                </h5>
                                <p class='text-sm text-dark'>Puntaje Anual: {{ number_format($usuario->puntaje, 2) }}
                                </p>
                                <div
                                    class="ribbon {{ $index == 0 ? 'ribbon-gold' : ($index == 1 ? 'ribbon-silver' : 'ribbon-bronze') }}">
                                    {{ $index == 0 ? '🥇' : ($index == 1 ? '🥈' : '🥉') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Estilos para los ribbons y efectos visuales -->
<style>
    .user-avatar {
        display: flex;
        justify-content: center;
    }

    .user-card .card {
        text-align: center;
    }

    .user-list-container {
        scroll-behavior: smooth;
    }

    /* <!-- CSS para el contenedor de scroll horizontal --> */
    .user-list-container {
        display: flex;
        overflow-x: auto;
        white-space: nowrap;
        gap: 10px;
        padding-bottom: 5px;
    }

    .user-card {
        display: inline-block;
        width: 324px;
        /* Ajusta el ancho según el tamaño deseado para cada tarjeta */
        vertical-align: top;
    }

    /* Evitar que el contenido se distorsione en el contenedor */
    .user-card .card {
        width: 100%;
    }

    .btn-link {
        text-decoration: underline;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .ribbon {
        position: absolute;
        top: -10px;
        right: -10px;
        padding: 10px;
        background-color: #fff;
        color: #333;
        font-size: 1.5rem;
        font-weight: bold;
        border-radius: 50%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .ribbon-gold {
        background-color: #FFD700;
        color: white;
    }

    .ribbon-silver {
        background-color: #C0C0C0;
        color: white;
    }

    .ribbon-bronze {
        background-color: #CD7F32;
        color: white;
    }

    .user-avatar img {
        border: 3px solid white;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .list-unstyled li {
        margin-bottom: 5px;
        color: #333;
    }
</style>
