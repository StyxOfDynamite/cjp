@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-xs font-medium text-zinc-400 uppercase tracking-wide']) }}>
    {{ $value ?? $slot }}
</label>
