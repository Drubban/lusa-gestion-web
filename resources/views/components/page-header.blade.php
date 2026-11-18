@props(['title', 'subtitle' => null, 'back' => null, 'backLabel' => 'Volver'])
<div class="page-head">
    <div>
        @if ($back)
            <div class="breadcrumb-lite"><a href="{{ $back }}"><i class="bi bi-arrow-left me-1"></i>{{ $backLabel }}</a></div>
        @endif
        <h1>{{ $title }}</h1>
        @if ($subtitle)<p>{{ $subtitle }}</p>@endif
    </div>
    @isset($actions)
        <div class="d-flex flex-wrap gap-2">{{ $actions }}</div>
    @endisset
</div>
