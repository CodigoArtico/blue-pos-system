<div class="card">
    <div class="card-header">
        <h3 class="card-heading text-dark fs-5">5 VENTAS MAS RECIENTES.</h3>
    </div>
    <div class="card-body px-0 pb-0">
        <div class="table-responsive">
            <table class='table mb-0' id="table1">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Fecha y Hora</th>
                        <th>Vendedor</th>
                        <th>Total</th>
                        <th><i class="fa-solid fa-gears"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ventas->sortByDesc('created_at')->take(5) as $item)
                        @if (\Carbon\Carbon::parse($item->created_at)->isToday())
                            <tr>
                                <td>
                                    <p class="text-muted mb-0">
                                        @if ($item->cliente && $item->cliente->persona)
                                        <i class="fa-solid fa-user-tie text-dark"></i> {{ $item->cliente->persona->razon_social }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </td>
                                <td>
                                    <div class="row-not-space">
                                        <p class="fw-semibold mb-0 text-dark">
                                            <span class="m-1"><i
                                                    class="fa-solid fa-clock"></i></span>
                                            {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }}
                                        </p>
                                    </div>
                                </td>
                                <td class="text-dark">
                                    <i class="fa-solid fa-circle-user"></i> {{ $item->user->name ?? 'No asignado' }}
                                </td>
                                <td class="text-dark">
                                    <i class="fa-solid fa-dollar-sign"></i> {{ $item->total }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group"
                                        aria-label="Basic mixed styles example">
                                        @can('mostrar-venta')
                                        {{-- Botón para abrir el modal --}}
                                        <button type="button" class="btn btn-outline-dark icon"
                                            data-bs-toggle="modal" data-bs-target="#modalTicket{{ $item->id }}">
                                            <i class="fa-solid fa-print"></i>
                                        </button>

                                        {{-- Incluir la vista parcial del modal --}}
                                        @include('venta.partials.ticket-venta', ['venta' => $item])
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
