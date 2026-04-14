<x-app-layout>
    <div class="max-w-2xl mx-auto space-y-6">

        <h1 class="text-2xl font-bold text-gray-800">Mi perfil</h1>

        {{-- Datos personales --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Información de la cuenta</h2>
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Cambiar contraseña --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-base font-semibold text-gray-700 mb-4">Cambiar contraseña</h2>
            @include('profile.partials.update-password-form')
        </div>

        {{-- Eliminar cuenta — solo si no es admin --}}
        @if(!auth()->user()->isAdmin())
        <div class="bg-white rounded-2xl shadow p-6 border border-red-100">
            <h2 class="text-base font-semibold text-red-600 mb-4">Zona de peligro</h2>
            @include('profile.partials.delete-user-form')
        </div>
        @endif

    </div>
</x-app-layout>