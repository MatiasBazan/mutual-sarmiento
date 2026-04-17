<x-app-layout>
<div style="max-width:560px; margin:0 auto; display:flex; flex-direction:column; gap:16px;">

    <h1 style="font-size:22px; font-weight:700; color:#fafafa; margin:0;">Mi perfil</h1>

    {{-- Información de la cuenta --}}
    <div style="background:#18181b; border:1px solid #27272a; border-radius:12px; padding:28px;">
        <h2 style="font-size:14px; font-weight:600; color:#fafafa; margin:0 0 4px;">Información de la cuenta</h2>
        <p style="font-size:13px; color:#71717a; margin:0 0 24px;">Actualizá tu nombre y correo electrónico.</p>
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Cambiar contraseña --}}
    <div style="background:#18181b; border:1px solid #27272a; border-radius:12px; padding:28px;">
        <h2 style="font-size:14px; font-weight:600; color:#fafafa; margin:0 0 4px;">Cambiar contraseña</h2>
        <p style="font-size:13px; color:#71717a; margin:0 0 24px;">Usá una contraseña larga y segura.</p>
        @include('profile.partials.update-password-form')
    </div>

</div>
</x-app-layout>
