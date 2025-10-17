@props(['title', 'value', 'change' => null, 'icon', 'iconColor' => 'text-purple-600'])

<div class="bg-white rounded-2xl shadow-md p-6 hover:shadow-xl transition-shadow duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-4">
                <div class="{{ $iconColor }}">
                    {{ $icon }}
                </div>
                <h3 class="text-gray-600 font-medium">{{ $title }}</h3>
            </div>
            <div class="flex items-end space-x-2">
                <p class="text-4xl font-bold text-gray-900">{{ $value }}</p>
                @if($change)
                    <span class="text-sm font-semibold mb-2 {{ str_starts_with($change, '+') ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
