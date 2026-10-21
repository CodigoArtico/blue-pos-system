<style>
    .custom-label {
        color: black;
        margin-bottom: 0.1rem;
        /* Ajusta este valor según tus necesidades */
    }

    /* Contenedor de la tabla con scroll vertical */
    .table-scrollable {
        max-height: 300px;
        /* Ajusta la altura según tus necesidades */
        overflow-y: auto;
        /* Opcional: mantener el ancho completo */
        width: 100%;
        border: 1px solid #ddd;
        /* Opcional: agregar borde al contenedor */
    }

    /* Hacer que la cabecera sea sticky */
    .table-scrollable thead th {
        position: sticky;
        top: 0;
        background-color: #343a40;
        /* Asegúrate de que el color coincida con bg-black */
        color: #fff;
        /* Color del texto de los encabezados */
        z-index: 2;
        /* Asegura que los encabezados estén por encima del contenido */
    }

    /* Opcional: estilos adicionales para mejorar la apariencia */
    .table-scrollable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table-scrollable tbody tr:hover {
        background-color: #e9ecef;
    }

    /* Estilos personalizados para la tabla de detalle */
    #tabla_detalle {
        margin-top: 2px;
        /* Margen superior */
        margin-bottom: 2px;
        /* Margen inferior */
        width: 100%;
        /* Ancho total */
        border-collapse: collapse;
        /* Colapsar bordes de la tabla */
    }

    #tabla_detalle th,
    #tabla_detalle td {
        padding: 2px;
        /* Espaciado interno */
        text-align: center;
        /* Alineación central */
        border: 1px solid #ddd;
        /* Borde de celda */
    }

    #tabla_detalle th {
        background-color: #f2f2f2;
        /* Fondo de encabezado */
        color: #333;
        /* Color de texto en encabezado */
    }

    /* Estilo especial para el nombre del producto */
    .producto-nombre {
        width: 150px;
        /* Ancho personalizado */
        /* white-space: nowrap; */
        /* Evita saltos de línea */
        overflow: hidden;
        /* Oculta el contenido que excede el ancho */
        text-overflow: ellipsis;
        /* Muestra puntos suspensivos si el texto es largo */
    }

    /* //estilo para el sect del clientes */
    /* Cambiar el color de fondo del contenedor de Select2 */
    .select2-container--default .select2-selection--single {
        background-color: #f0f8ff;
        border: 2px solid #66afe9;
        border-radius: 8px;
        height: 45px;
        /* Ajusta la altura según tus necesidades */
    }

    /* Cambiar el color del texto */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333333;
        line-height: 45px;
        /* Debe coincidir con la altura del contenedor */
    }

    /* Cambiar el color del borde al enfocar */
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #1c98fd;
        box-shadow: 0 0 5px rgba(102, 175, 233, 0.6);
    }

    /* Opciones desplegadas */
    .select2-container--default .select2-results__option {
        background-color: #ffffff;
        color: #333333;
    }

    /* Opciones al pasar el cursor */
    .select2-container--default .select2-results__option--highlighted {
        background-color: #e6f7ff;
        color: #000000;
    }

    /* Aumentar el tamaño del botón de limpieza ('x') en Select2 de selección única */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        padding: 4px;
        /* Puedes ajustar el padding para que la X quede centrada */
        font-size: 28px;
        /* Tamaño de fuente ajustado */
        width: 22px;
        /* Ancho aumentado para acomodar el 'x' */
        height: 20px;
        /* Altura aumentada para acomodar el 'x' */
        line-height: 24px;
        /* Coincide con la altura para centrar verticalmente */
        color: #000000;
        /* Color del 'x' cambiado a blanco para mejor contraste */
        /* background-color: #f36d6d; */
        /* Color de fondo rojo */
        border-radius: 50%;
        /* Hace que el botón sea circular */
        position: absolute;
        right: 5px;
        /* Ajusta la posición horizontal para centrar el círculo */
        top: 50%;
        transform: translateY(-50%);
        text-align: center;
        cursor: pointer;
        transition: background-color 0.3s ease;

        /* Utilizar Flexbox para centrar el 'x' */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Cambiar el color del 'x' al pasar el cursor */
    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        background-color: #cc0000;
        border-radius: 50%;
        /* Hace que el fondo sea redondeado */
        padding: 4px;
        /* Puedes ajustar el padding para que la X quede centrada */
        /* Rojo más oscuro al pasar el cursor */
    }
</style>
