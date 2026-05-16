<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent font-semibold text-xs text-black uppercase tracking-widest hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:ring-offset-zinc-900 active:bg-amber-600 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
