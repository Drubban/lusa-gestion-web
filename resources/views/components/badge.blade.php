@props(['tone' => 'muted', 'dot' => false])
<span {{ $attributes->class("badge-soft tone-{$tone}") }}>@if($dot)<span class="dot"></span>@endif{{ $slot }}</span>
