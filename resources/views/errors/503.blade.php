<x-layouts.app hide-menu>
    <x-ui.alert-warning>
        <x-slot name="title">Site inaccessible</x-slot>
        {{ config('app.maintenance.message') }}
    </x-ui.alert-warning>
</x-layouts.app>
