@props(['size' => 'md'])

@php
    $sizes = [
        'sm' => ['w' => 16,  'h' => 23,  'text' => 13],
        'md' => ['w' => 26,  'h' => 38,  'text' => 18],
        'lg' => ['w' => 36,  'h' => 52,  'text' => 24],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
    $cls = 'logo' . ($size !== 'md' ? ' logo--'.$size : '');
@endphp

<span class="{{ $cls }}">
    <span class="logo__icon">
        <svg width="{{ $s['w'] }}" height="{{ $s['h'] }}" viewBox="0 0 26 38" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1.5 1.5 H24.5 V35.5 L13 27.5 L1.5 35.5 Z"
                  fill="var(--accent)" fill-opacity="0.10"
                  stroke="var(--accent)" stroke-width="1.6"
                  stroke-linejoin="round" stroke-linecap="round"/>
            <line x1="7" y1="12" x2="19" y2="12"
                  stroke="var(--accent)" stroke-width="1.1"
                  stroke-linecap="round" opacity="0.65"/>
            <line x1="7" y1="17" x2="15" y2="17"
                  stroke="var(--accent)" stroke-width="1.1"
                  stroke-linecap="round" opacity="0.4"/>
        </svg>
    </span>
    <span class="logo__wordmark" style="font-size: {{ $s['text'] }}px">
        <span class="logo__read">Read</span><span class="logo__on">On</span>
    </span>
</span>
