@props(['paginator'])
@if ($paginator->hasPages())
    <div class="card-body border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span class="small text-muted">Mostrando {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} de {{ $paginator->total() }}</span>
        {{ $paginator->links() }}
    </div>
@endif
