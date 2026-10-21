{{-- ESILOS DE WEB EXTERNO --}}
<style>
    /* Dropdown menu styling */
    .dropdown-menu-web {
        min-width: 200px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 1px;
    }

    /* List group item */
    .list-group-item {
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Web link styling */
    .web-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
        color: #007bff;
        /* Bootstrap primary color */
        text-decoration: none;
    }

    .web-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Icon inside link */
    .web-link i {
        font-size: 1.2rem;
        color: #007bff;
    }

    /* Globe icon styling */
    .fa-globe {
        color: #007bff;
        transition: transform 0.2s ease-in-out, color 0.2s ease-in-out;
    }

    .fa-globe:hover {
        transform: scale(1.1);
        color: #0056b3;
    }

    /* Dropdown menu hover effect */
    .dropdown-menu-web:hover {
        box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.2);
    }
</style>
{{-- ESTILOS DE CAMPANA  --}}
<style>
    /* Notificación en el icono */
    .notification-dot-bell {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 10px;
        height: 10px;
        border: 2px solid white;
        /* Borde blanco para destacar el puntito */
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
        z-index: 1;
    }

    /* Celeste */
    .notification-dot-blue {
        background-color: #00c8ff;
        box-shadow: 0 0 8px rgba(0, 200, 255, 0.6);
    }

    /* Rojo para notificación */
    .notification-dot-red {
        background-color: #ff4d4d;
        box-shadow: 0 0 8px rgba(255, 77, 77, 0.6);
    }


    /* Menú desplegable estilizado */
    .dropdown-menu-large {
        min-width: 230px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .list-group-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .list-group-item h6 {
        font-weight: bold;
        font-size: 1rem;
    }

    .list-group-item p {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        color: #555;
    }

    .avatar-ordenes {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
    }

    .dropdown-item.active {
        font-size: 0.85rem;
        padding: 5px 10px;
        display: inline-block;
        text-decoration: none;
    }
</style>
{{-- ESTILOS DE MENSAJERIA  --}}
<style>
    /* Notificación en el icono */
    .notification-dot {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 10px;
        height: 10px;
        background-color: red;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(255, 0, 0, 0.6);
        z-index: 1;
    }

    /* Menú desplegable estilizado */
    .notification-menu {
        min-width: 250px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    .notification-menu h6 {
        font-weight: bold;
        font-size: 1rem;
    }

    .notification-menu p {
        font-size: 0.9rem;
        color: #555;
    }

    .notification-menu .btn {
        font-size: 0.85rem;
        padding: 5px 10px;
    }

    .notification-menu .text-muted {
        font-style: italic;
    }
</style>
