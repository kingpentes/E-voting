@props(['title', 'value', 'change' => null, 'icon', 'iconColor' => 'text-purple-600'])

<div class="bg-white rounded-xl sm:rounded-2xl shadow-md p-4 sm:p-6 hover:shadow-xl transition-shadow duration-300">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-2 sm:mb-4">
                <div class="{{ $iconColor }} flex-shrink-0">
                    {{ $icon }}
                </div>
                <h3 class="text-gray-600 font-medium text-xs sm:text-sm lg:text-base truncate">{{ $title }}</h3>
            </div>
            <div class="flex items-end space-x-2 flex-wrap">
                <p class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">{{ $value }}</p>
                @if($change)
                    <span class="text-xs sm:text-sm font-semibold mb-1 sm:mb-2 {{ str_starts_with($change, '+') ? 'text-green-600' : 'text-red-600' }}">
                        {{ $change }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
