<div {{ $attributes->merge(['class' => '']) }}>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-base-content">{{ $title }}</h3>
                <p class="mt-1 text-sm text-base-content/60">{{ $description }}</p>
            </div>
        </div>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="bg-base-200 px-4 py-5 sm:p-6 shadow sm:rounded-lg">
                {{ $content }}
            </div>
        </div>
    </div>
</div>
