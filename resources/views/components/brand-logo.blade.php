@props([
    'href' => url('/'),
    'class' => '',
    'label' => null,
])

<a href="{{ $href }}" class="wordmark {{ $class }}" aria-label="{{ $label ?? ($branding?->nom ?? config('app.name')) }}">
    @if ($branding?->logo)
        <img class="brand-logo-image" src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($branding->logo) }}" alt="{{ $branding->nom }}">
    @else
        {{ $branding?->nom ?? config('app.name') }}<span>.</span>
    @endif
</a>