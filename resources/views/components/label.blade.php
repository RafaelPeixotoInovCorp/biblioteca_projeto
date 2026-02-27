@props(['value'])

<label {{ $attributes->merge(['class' => 'label-text']) }}>
    {{ $value ?? $slot }}
</label>
