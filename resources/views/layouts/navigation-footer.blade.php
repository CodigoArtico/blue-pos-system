<style>
    /* Contenedor principal del footer */
    .custom-footer {
        text-align: center;
        background-color: #f4f6f9;
        /* Fondo suave y claro */
        padding: 4px 0;
        /* Relleno para espacio vertical */
        border-top: 2px solid #e2e6ea;
        /* Borde suave para separar el footer */
    }

    /* Texto de la versión con el año dinámico */
    .custom-footer-version {
        margin: 5px 0;
        /* Reducir el espaciado vertical entre el texto y los íconos */
        font-size: 15px;
        /* Aumento del tamaño de la fuente */
        line-height: 1.5;
        /* Mejora la legibilidad */
        color: #000000;
        /* Color gris suave */
        font-weight: 400;
        /* Peso de la fuente más ligero */
    }

    .custom-footer-version a {
        color: #007bff;
        /* Azul para el enlace */
        text-decoration: none;
        /* Eliminar subrayado */
        font-weight: 600;
        /* Resaltar el enlace */
    }

    .custom-footer-version a:hover {
        text-decoration: underline;
        /* Subrayado al pasar el ratón */
    }

    /* Contenedor de los íconos sociales */
    .custom-social-icons {
        display: flex;
        justify-content: center;
        gap: 25px;
        /* Reducir el espacio entre los iconos */
        margin-top: 5px;
        /* Reducir el espacio entre los iconos y el texto */
    }

    .custom-social-icons a {
        color: #000000;
        /* Color gris oscuro para los íconos */
        font-size: 24px;
        /* Aumento del tamaño de los íconos */
        transition: transform 0.3s ease, color 0.3s ease;
        /* Animaciones suaves */
    }

    .custom-social-icons a:hover {
        transform: scale(1.3);
        /* Aumentar tamaño al pasar el ratón */
        color: #007bff;
        /* Cambio de color al pasar el ratón */
    }
</style>

<footer>
    <div class="custom-footer">
        <!-- Texto de la versión con el año dinámico usando Blade -->
        <h6 class="custom-footer-version">
            V3.3.9 {{ date('Y') }} &copy; | By
            <a target="_blank" href="https://codart-store.deviper.net/">Codigo-Artico</a>
        </h6>
        <!-- Íconos sociales -->
        <div class="custom-social-icons">
            <a href="{{ config('services.social.instagram', env('INSTAGRAM_URL', 'https://www.instagram.com/codigo_artico/')) }}" target="_blank" title="Instagram">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="{{ config('services.social.youtube', env('YOUTUBE_URL', 'https://youtube.com/@codigoartico?si=Ka3_NzcINLMgl_KI')) }}" target="_blank" title="YouTube">
                <i class="fab fa-youtube"></i>
            </a>
            <a href="{{ config('services.social.github', env('GITHUB_URL', 'https://github.com/CodigoArtico?tab=repositories')) }}" target="_blank" title="GitHub">
                <i class="fab fa-github"></i>
            </a>
            <a href="https://www.tiktok.com/@cdigo.rtico" target="_blank" title="TikTok">
                <i class="fab fa-tiktok"></i>
            </a>
            <a href="{{ !empty($licenciaUsuario->telf_soporte) ? 'https://wa.me/' . $licenciaUsuario->telf_soporte : config('services.telegram.url', env('TELEGRAM_URL', 'https://t.me/codigoartico')) }}" target="_blank"
                title="WhatsApp">
                <i class="fab fa-whatsapp"></i>
            </a>
        </div>
    </div>
</footer>
