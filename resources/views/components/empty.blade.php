@props(['icon' => 'bi-inbox', 'text' => 'Sin registros para mostrar.'])
<div class="empty"><i class="bi {{ $icon }}"></i>{{ $text }}{{ $slot }}</div>
