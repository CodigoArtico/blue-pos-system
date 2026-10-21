<x-guest-layout>

    {{-- /* (estilos que ya tienes) */ --}}
    <link rel="stylesheet" href="{{asset('css/login/login.css')}}">

    

    <div class="login-container">
        <img src="{{ asset('images/banner2.png') }}" alt="Imagen descriptiva" class="bottom-image">
        <h1>Recuperar Contraseña</h1>
        <p>¿Olvidaste tu contraseña? No hay problema. Ingresa tu email y te enviaremos un enlace para restablecerla.</p>

        <!-- Mostrar mensaje de éxito (cuando el enlace se envíe correctamente) -->
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <!-- Formulario de recuperación de contraseña -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Campo de Email -->
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" class="input-field" :value="old('email')" required autofocus autocomplete="username" placeholder="Email">
                <!-- Mostrar el error específico del campo email -->
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <button type="submit" class="login-btn">
                {{ __('Enviar enlace de restablecimiento de contraseña') }}
            </button>
        </form>

        <!-- Enlace para iniciar sesión -->
        <div class="options">
            @if (Route::has('login'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('¿Ya Tienes Cuenta?') }}
                </a>
            @endif
        </div>
    </div>
</x-guest-layout>
