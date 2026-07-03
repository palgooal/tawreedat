@php
    $tawreedatUser = filament()->auth()->user();
@endphp

@if ($tawreedatUser)
    <span class="tawreedat-user-info">
        <span class="tawreedat-user-info-name">{{ filament()->getUserName($tawreedatUser) }}</span>
        <span class="tawreedat-user-info-role">مدير النظام</span>
    </span>
@endif
