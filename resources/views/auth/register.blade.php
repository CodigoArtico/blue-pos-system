<x-guest-layout>
    @section('title', 'Registrarme')
 
     <!-- Video de fondo -->
     
     <!-- Imagen de respaldo que aparece antes de que el video se cargue -->
    <img src="{{ asset('images/login1.webp') }}" class="background-image-fallback" alt="Imagen de respaldo" />
 
     <!-- Contenedor del formulario de registro -->
     <div class="login-container">
         <!-- Título y descripción -->
         <img src="{{ asset('images/banner2.png') }}" alt="Imagen descriptiva" class="bottom-image">
         <h1>Registro de Usuario</h1>
         <p>Por favor, completa los campos para crear una cuenta.</p>
 
         <!-- Formulario de registro -->
         <form method="POST" action="{{ route('register') }}">
             @csrf
 
             <!-- Campo de Nombre -->
             <div>
                 <label for="name">Nombre</label>
                 <input id="name" type="text" name="name" class="input-field" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Nombre">
                 <x-input-error :messages="$errors->get('name')" class="mt-2" />
             </div>
 
             <!-- Campo de Email -->
             <div class="mt-4">
                 <label for="email">Email</label>
                 <input id="email" type="email" name="email" class="input-field" value="{{ old('email') }}" required autocomplete="username" placeholder="Email">
                 <x-input-error :messages="$errors->get('email')" class="mt-2" />
             </div>
 
             <!-- Campo de Contraseña -->
             <div class="mt-4">
                 <label for="password">Contraseña</label>
                 <input id="password" type="password" name="password" class="input-field" required autocomplete="new-password" placeholder="Contraseña">
                 <x-input-error :messages="$errors->get('password')" class="mt-2" />
             </div>
 
             <!-- Confirmación de Contraseña -->
             <div class="mt-4">
                 <label for="password_confirmation">Confirmar Contraseña</label>
                 <input id="password_confirmation" type="password" name="password_confirmation" class="input-field" required autocomplete="new-password" placeholder="Confirmar Contraseña">
                 <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
             </div>
 
             <!-- Enlace de inicio de sesión y botón de registro -->
             <div class="flex items-center justify-end mt-4">
                <a href="{{ route('login') }}" style="color: white; text-decoration: underline; transition: color 0.3s;">
                    Ya Tienes una Cuenta? Iniciar Sesion
                </a>                
 
                 <button type="submit" class="login-btn ms-4">
                     {{ __('Registrarse') }}
                 </button>
             </div>
         </form>
     </div>
 </x-guest-layout>
 