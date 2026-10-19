@props([
    'title' => null,
    'subtitle' => null,
    'titleId' => null,
    'actions' => null,
])

<div class="topbar">
    <div>
        <h1 @if(!empty($titleId)) id="{{ $titleId }}" @endif>{!! $title ?? '' !!}</h1>
        @if(!empty($subtitle))
            <div class="subtitle">{!! $subtitle !!}</div>
        @endif
    </div>
    @if(isset($actions) && trim((string)$actions) !== '')
        <div class="topbar-actions">
            {!! $actions !!}
        </div>
    @elseif(!empty($slot) && trim((string)$slot) !== '')
        <div class="topbar-actions">
            {{ $slot }}
        </div>
    @endif
</div>
