<style>
    /* Estilos generales */
    body {
        font-family: 'Inter', sans-serif; /* Fuente moderna */
        /* background-color: #f8f9fa; Fondo suave */
        margin: 0;
        padding: 0;
    }

    /* Estilo para la tarjeta */
    .card {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0px 4px 24px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-header {
        background: linear-gradient(135deg, #00b7ff, #0099cc); /* Degradado moderno */
        color: #fff;
        padding: 8px;
        text-align: center;
        margin-bottom: 12px;
    }

    .card-heading {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
    }

    .card-body {
        padding: 24px;
    }

    h1 {
        color: #2d3748; /* Gris oscuro */
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .text-green {
        color: #34d399; /* Verde moderno */
    }

    .text-red {
        color: #ef4444; /* Rojo moderno */
    }

    .text-xs {
        font-size: 0.875rem;
        color: #6b7280; /* Gris medio */
    }

    /* Leyendas dinámicas */
    .legends {
        margin-top: 24px;
    }

    .legend {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
    }

    .legend .w-3 {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .bg-info {
        background-color: #00b7ff; /* Azul claro */
    }

    .bg-blue {
        background-color: #3b82f6; /* Azul moderno */
    }

    /* Canvas (gráfico) */
    canvas {
        max-width: 100%;
        height: auto !important;
    }

    /* Responsividad */
    @media (max-width: 768px) {
        .card-header {
            padding: 16px;
        }

        h1 {
            font-size: 1rem;
        }

        .card-body {
            padding: 16px;
        }

        .col-md-4, .col-md-8 {
            width: 100%;
        }
    }
</style>
<script>
    // Datos de los productos más vendidos
  var productosPorMes = @json($productosAgrupados);
  var meses = @json($meses); // Los meses ya calculados dinámicamente
//   console.log(productosPorMes);
//   console.log(meses);
</script>

<div class="card">
    <div class="card-header">
        <h3 class="card-heading text-dark fs-5">PROD. MÁS VENDIDOS POR MES</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-12">
                <div class="pl-3">
                    <h1>S/.{{ number_format($montoMesActual, 2) }}</h1>
                    <p class="text-xs">
                        <span class="{{ $subidaPorcentaje >= 0 ? 'text-green' : 'text-red' }}">
                            <i data-feather="bar-chart" width="15"></i>
                            {{ number_format($subidaPorcentaje, 2) }}%
                        </span> Este mes vs. anterior
                    </p>

                    <div class="legends">
                        <div class="legend">
                            <div class="w-3 h-3 rounded-full bg-info"></div>
                            <span class="text-xs">{{ $productoMesActual->nombre ?? 'N/A' }}</span>
                        </div>
                        <div class="legend">
                            <div class="w-3 h-3 rounded-full bg-blue"></div>
                            <span class="text-xs">{{ $productoMesAnterior->nombre ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-12">
                <canvas id="bar"></canvas>
            </div>
        </div>
    </div>
</div>