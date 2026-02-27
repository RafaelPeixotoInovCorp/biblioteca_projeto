@props(['submit'])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-base-content">{{ $title }}</h3>
                <p class="mt-1 text-sm text-base-content/60">{{ $description }}</p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <form wire:submit="{{ $submit }}">
                <div class="bg-base-200 px-4 py-5 sm:p-6 shadow sm:rounded-lg">
                    <div class="grid grid-cols-6 gap-6">
                        {{ $form }}
                    </div>
                </div>

                @if (isset($actions))
                    <div class="flex items-center justify-end px-4 py-3 bg-base-300 text-end sm:px-6 shadow sm:rounded-bl-lg sm:rounded-br-lg">
                        {{ $actions }}
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
