@php
    /** @var \App\Models\OrganizationNode $node */
    $featured = $featured ?? false;
@endphp
<a href="#org-{{ $node->slug }}" class="group flex gap-4 overflow-hidden rounded-2xl border p-4 transition hover:-translate-y-0.5 hover:shadow-md {{ $featured ? 'border-kemenag/25 bg-white shadow-sm sm:col-span-1' : 'border-kemenag/10 bg-surface' }}">
    <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-kemenag-soft sm:h-28 sm:w-28">
        @if ($node->photo)
            <img src="{{ asset('storage/'.$node->photo) }}" alt="{{ $node->name ?: $node->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center font-display text-3xl font-extrabold text-kemenag/30">
                {{ $node->initials() }}
            </div>
        @endif
    </div>
    <div class="min-w-0 self-center">
        <p class="text-[11px] font-bold uppercase tracking-[0.16em] {{ $featured ? 'text-gold' : 'text-kemenag' }}">{{ $node->title }}</p>
        <h2 class="mt-1 font-display text-xl font-extrabold leading-snug text-kemenag-deep sm:text-2xl">
            {{ $node->name ?: 'Belum diisi' }}
        </h2>
        @if ($node->description)
            <p class="mt-2 line-clamp-2 text-sm text-muted">{{ $node->description }}</p>
        @endif
    </div>
</a>
