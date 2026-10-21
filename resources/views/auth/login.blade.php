<x-guest-layout>
    @section('title', 'Iniciar Sesión')

    <!-- Imagen de fondo -->
    <img loading="lazy" src="{{ asset('images/login1.webp') }}" class="background-image-fallback" alt="Fondo de login">

    <!-- Contenedor principal -->
    <div class="login-container">
        <img loading="lazy" src="{{ $imgAccount ?? asset('images/banner2.png') }}" alt="Logo Empresa" class="bottom-image">

        <h1>Acceso al Sistema</h1>
        <p>Por favor ingresa tus datos para continuar.</p>

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Email -->
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" class="input-field"
                       value="{{ old('email', 'Administrador@gmail.com') }}"
                       required autofocus autocomplete="username"
                       placeholder="Email">
                <x-input-error :messages="$errors->get('email')" class="mt-2" style="font-size: 0.75rem; color: red;" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" class="input-field"
                       required autocomplete="current-password"
                       placeholder="Contraseña" value="12345678">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Recordarme -->
            <div class="block mt-4">
                <input id="remember_me" type="checkbox" class="styled-checkbox" name="remember">
                <label for="remember_me" class="checkbox-label">Recordarme</label>
            </div>

            <!-- Botón -->
            <button type="submit" id="login-button" class="login-btn">
                Iniciar Sesión
            </button>
        </form>

        <!-- Redes sociales -->
        <p class="mt-1">— O inicia sesión con —</p>
        <div class="social-login">
            <a href="#" class="social-btn google"><i class="fa-brands fa-google"></i> Google</a>
            <a href="#" class="social-btn github"><i class="fa-brands fa-github"></i> GitHub</a>
        </div>
    </div>
</x-guest-layout>
