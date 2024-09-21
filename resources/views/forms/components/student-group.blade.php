<div>
    <x-filament::modal id="open-schedules" width="3xl">

        <x-slot name="heading">
            Installation schedules
        </x-slot>

        {{ $this->table }}

    </x-filament::modal>

    <x-filament::button @click="$dispatch('open-modal', {id: 'open-schedules'})">
        Update Schedule
    </x-filament::button>

</div
