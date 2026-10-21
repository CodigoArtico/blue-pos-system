<div class="dashboard">
    <!-- Ventas -->
    <div class="dashboard-card dashboard-ventas" onclick="location.href='{{ route('ventas.create') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-store"></i><span>Realizar Venta</span>
            </div>
            <p class="dashboard-cantidad">{{ $cantidad_ventas }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Categorías -->
    <div class="dashboard-card dashboard-categorias" onclick="location.href='{{ route('categorias.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-tag"></i><span>Categorías</span>
            </div>
            <p class="dashboard-cantidad">{{ $categorias }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Compras -->
    <div class="dashboard-card dashboard-compras" onclick="location.href='{{ route('compras.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-store"></i><span>Compras</span>
            </div>
            <p class="dashboard-cantidad">{{ $compras }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Marcas -->
    <div class="dashboard-card dashboard-marcas" onclick="location.href='{{ route('marcas.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-bullhorn"></i><span>Marcas</span>
            </div>
            <p class="dashboard-cantidad">{{ $marcas }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Presentaciones -->
    <div class="dashboard-card dashboard-presentaciones" onclick="location.href='{{ route('presentaciones.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-box-archive"></i><span>Presentaciones</span>
            </div>
            <p class="dashboard-cantidad">{{ $presentaciones }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Productos -->
    <div class="dashboard-card dashboard-productos" onclick="location.href='{{ route('productos.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-brands fa-shopify"></i><span>Productos</span>
            </div>
            <p class="dashboard-cantidad">{{ $productos }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Proveedores -->
    <div class="dashboard-card dashboard-proveedores" onclick="location.href='{{ route('proveedores.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-user-group"></i><span>Proveedores</span>
            </div>
            <p class="dashboard-cantidad">{{ $proveedores }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>

    <!-- Clientes -->
    <div class="dashboard-card dashboard-clientes" onclick="location.href='{{ route('clientes.index') }}'">
        <div class="dashboard-card-body">
            <div class="dashboard-info">
                <i class="fa-solid fa-people-group"></i><span>Clientes</span>
            </div>
            <p class="dashboard-cantidad">{{ $clientes }}</p>
        </div>
        <div class="dashboard-card-footer">
            <span>Ver más</span>
            <i class="fas fa-angle-right"></i>
        </div>
    </div>
</div>

<style>
    /* Estilos generales */
    body {
        font-family: Arial, sans-serif;
        /* background-color: #f4f4f4; */
        margin: 0;
        padding: 0px;
    }

    .dashboard {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        /* 4 tarjetas por fila en pantallas grandes */
        gap: 20px;
        max-width: 1600px;
        margin: auto;
    }

    /* Para tablets (2 tarjetas por fila) */
    @media (max-width: 1024px) {
        .dashboard {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Para móviles (1 tarjeta por fila) */
    @media (max-width: 600px) {
        .dashboard {
            grid-template-columns: repeat(1, 1fr);
        }
    }

    /* Estilos para las tarjetas */
    .dashboard-card {
        background: #fff;
        border-radius: 8px;
        border: 3px solid;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease-in-out;
        color: #000;
        cursor: pointer;
        /* Hace que la tarjeta parezca un enlace */
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
    }

    /* Estilos internos */
    .dashboard-card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dashboard-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: bold;
    }

    .dashboard-info i {
        font-size: 20px;
    }

    .dashboard-cantidad {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
    }

    .dashboard-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 0px;
        font-weight: bold;
    }

    /* Evita el color azul del enlace */
    .dashboard-card-footer a {
        text-decoration: none;
        color: inherit;
    }

    /* Colores personalizados para cada tarjeta */
    .dashboard-ventas {
        border-color: #3498db;
    }

    .dashboard-categorias {
        border-color: #e67e22;
    }

    .dashboard-compras {
        border-color: #27ae60;
    }

    .dashboard-marcas {
        border-color: #7f8c8d;
    }

    .dashboard-presentaciones {
        border-color: #2c3e50;
    }

    .dashboard-productos {
        border-color: #c0392b;
    }

    .dashboard-proveedores {
        border-color: #aeff00;
    }

    .dashboard-clientes {
        border-color: #8e44ad;
    }
</style>
