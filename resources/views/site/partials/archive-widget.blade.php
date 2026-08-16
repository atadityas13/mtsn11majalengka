@php
    use Carbon\Carbon;

    $archivePage = (int) ($archives['page'] ?? 0);
    $archiveMonths = $archives['months'] ?? collect();
    $hasPrev = (bool) ($archives['has_prev'] ?? false);
    $hasNext = (bool) ($archives['has_next'] ?? false);

    $archiveBaseParams = array_filter([
        'q' => ($search ?? null) ?: null,
        'kategori' => ($activeCategory ?? null) ?: null,
    ]);
@endphp

<div class="sidebar-card">
    <h2 class="sidebar-card-title">Daftar Arsip</h2>
    <div class="mt-4 space-y-1">
        @forelse ($archiveMonths as $item)
            @php
                $label = Carbon::create((int) $item->year, (int) $item->month, 1)
                    ->locale('id')
                    ->translatedFormat('F Y');
                $isActive = (int) ($activeYear ?? 0) === (int) $item->year
                    && (int) ($activeMonth ?? 0) === (int) $item->month;
            @endphp
            <a
                href="{{ route('posts.index', array_merge($archiveBaseParams, ['tahun' => $item->year, 'bulan' => $item->month])) }}"
                class="flex items-center justify-between gap-3 rounded-lg px-2 py-2 text-sm transition hover:bg-kemenag-soft/70 {{ $isActive ? 'bg-kemenag-soft font-bold text-kemenag' : 'text-kemenag-deep' }}"
            >
                <span>{{ $label }}</span>
                <span class="rounded-full bg-kemenag/10 px-2 py-0.5 text-[11px] font-bold text-kemenag">{{ number_format((int) $item->posts_count) }}</span>
            </a>
        @empty
            <p class="text-sm text-muted">Belum ada arsip berita.</p>
        @endforelse
    </div>

    @if ($hasPrev || $hasNext)
        <div class="mt-4 flex items-center justify-between gap-2 border-t border-kemenag/10 pt-3 text-xs font-bold">
            @if ($hasPrev)
                <a href="{{ route('posts.index', array_merge($archiveBaseParams, array_filter(['tahun' => $activeYear ?: null, 'bulan' => $activeMonth ?: null, 'arsip' => $archivePage - 1 ?: null]))) }}" class="text-kemenag hover:underline">
                    ← Sebelumnya
                </a>
            @else
                <span></span>
            @endif
            @if ($hasNext)
                <a href="{{ route('posts.index', array_merge($archiveBaseParams, array_filter(['tahun' => $activeYear ?: null, 'bulan' => $activeMonth ?: null, 'arsip' => $archivePage + 1]))) }}" class="text-kemenag hover:underline">
                    Selanjutnya →
                </a>
            @endif
        </div>
    @endif
</div>
