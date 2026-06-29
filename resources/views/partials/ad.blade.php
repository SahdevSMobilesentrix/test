@php
    $client = config('blog.adsense.client');
    $slot = config('blog.adsense.slots.'.($slotKey ?? 'in_article'));
    $label = $label ?? 'sponsored';
    $format = $format ?? 'auto';
@endphp

<aside class="ad-slot ad-{{ $slotKey ?? 'in_article' }}" aria-label="advertisement">
    <span class="ad-label">Advertisement</span>
    @if ($client && $slot)
        <ins class="adsbygoogle"
             style="display:block"
             data-ad-client="{{ $client }}"
             data-ad-slot="{{ $slot }}"
             data-ad-format="{{ $format }}"
             data-full-width-responsive="true"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
    @else
        <div class="ad-placeholder">Ad space &mdash; {{ $label }}</div>
    @endif
</aside>
