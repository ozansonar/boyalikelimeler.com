@props([
    'color' => 'blue',
    'icon',
    'label',
    'count',
    'delay' => 0,
    'colClass' => 'col-xl-3 col-sm-6',
])

<div class="{{ $colClass }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">
    <div class="usr-stat-card">
        <div class="usr-stat-icon usr-stat-icon-{{ $color }}">
            <i class="bi {{ $icon }}"></i>
        </div>
        <div class="usr-stat-info">
            <span class="usr-stat-label">{{ $label }}</span>
            <h3 class="usr-stat-value" data-count="{{ $count }}">0</h3>
        </div>
    </div>
</div>
