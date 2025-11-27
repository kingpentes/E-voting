@props(['title', 'value', 'change' => null, 'icon', 'iconColor' => 'text-blue-600'])

<div
    class="glass-strong rounded-2xl shadow-glass hover:shadow-glow-lg transition-all duration-300 p-6 hover-lift animate-scale-in border border-white/30">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <div class="flex items-center space-x-3 mb-4">
                <div class="p-2.5 rounded-xl bg-gradient-to-br from-blue-600 to-cyan-600 shadow-lg">
                    <div class="text-white">
                        {{ $icon }}
                    </div>
                </div>
                <h3 class="text-gray-700 font-semibold text-sm uppercase tracking-wide">{{ $title }}</h3>
            </div>
            <div class="flex items-end space-x-2">
                <p class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                    {{ $value }}</p>
                @if ($change)
                    <span
                        class="text-sm font-bold mb-2 px-2 py-0.5 rounded-lg {{ str_contains($change, '%') || str_starts_with($change, '+') ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $change }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
