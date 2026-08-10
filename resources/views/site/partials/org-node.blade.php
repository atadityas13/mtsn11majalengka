@php
    /** @var \App\Models\OrganizationNode $node */
    $variant = $variant ?? 'line';
@endphp
<a href="#org-{{ $node->slug }}" class="org-node org-node-{{ $variant }} group">
    <div class="org-node-photo">
        @if ($node->photo)
            <img src="{{ asset('storage/'.$node->photo) }}" alt="{{ $node->name ?: $node->title }}">
        @else
            <span>{{ $node->initials() }}</span>
        @endif
    </div>
    <div class="org-node-body">
        <p class="org-node-title">{{ $node->title }}</p>
        <p class="org-node-name">{{ $node->name ?: 'Belum diisi' }}</p>
    </div>
</a>
