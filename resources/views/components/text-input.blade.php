@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white/5 border-white/10 text-zinc-100 placeholder-zinc-500 focus:border-amber-500 focus:ring-amber-500 rounded-none shadow-none']) }}>
