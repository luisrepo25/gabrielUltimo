<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-teal-700 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest shadow-md shadow-teal-700/25 hover:bg-teal-600 focus:bg-teal-600 active:bg-teal-800 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
