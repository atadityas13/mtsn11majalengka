@php
    /** @var \App\Models\OrganizationNode $node */
    $accent = $accent ?? 'green';
@endphp
<a href="#org-{{ $node->slug }}" class="tf-nc {{ $accent === 'gold' ? 'tf-nc-gold' : '' }}">
    <span class="tf-avatar">
        @if ($node->photo)
            <img src="{{ asset('storage/'.$node->photo) }}" alt="">
        @else
            {{ $node->initials() }}
        @endif
    </span>
    <span class="tf-role">{{ $node->title }}</span>
    <span class="tf-name">{{ $node->name ?: 'Belum diisi' }}</span>
</a>
