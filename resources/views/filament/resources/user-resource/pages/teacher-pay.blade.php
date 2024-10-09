<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::card>
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Xulosa
                </h2>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Balans') }}
                    </p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ format_money($this->record->balans) }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Jami hisoblangan summa') }}
                    </p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ format_money($this->data) }}
                    </p>
                </div>
            </div>
        </x-filament::card>
    </div>
    {{ $this->table }}
</x-filament-panels::page>