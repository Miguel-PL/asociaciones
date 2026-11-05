<x-filament-panels::page>
    <form wire:submit="save" class="association-settings-form">
        {{ $this->form }}

        <div class="association-settings-actions">
            <x-filament::button type="submit">Guardar cambios</x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
