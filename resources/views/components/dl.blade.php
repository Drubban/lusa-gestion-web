@props(['items' => []])
<dl class="dl-grid">
    @foreach ($items as $etiqueta => $valor)
        <dt>{{ $etiqueta }}</dt>
        <dd>{{ ($valor === null || $valor === '') ? '-' : $valor }}</dd>
    @endforeach
    {{ $slot }}
</dl>
