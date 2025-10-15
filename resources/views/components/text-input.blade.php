@props(['type' => 'text', 'name', 'placeholder' => '', 'required' => false])

<input type="{{ $type }}" 
       name="{{ $name }}"
       placeholder="{{ $placeholder }}"
       {{ $required ? 'required' : '' }}
       class="w-full border p-2 rounded focus:ring focus:ring-blue-400">
