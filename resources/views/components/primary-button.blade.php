@props(['color' => 'blue'])

<button {{ $attributes->merge(['class' => "w-full bg-$color-600 text-white p-2 rounded hover:bg-$color-700"]) }}>
    {{ $slot }}
</button>