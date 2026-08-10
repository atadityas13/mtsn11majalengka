@extends('layouts.site')

@section('title', 'Agenda — '.$site->school_name)
@section('description', 'Kalender kegiatan '.$site->school_name)

@section('content')
<div class="border-b border-kemenag/10 bg-kemenag-deep text-white">
    <div class="site-container py-12">
        <h1 class="font-display text-4xl font-extrabold md:text-5xl">Agenda</h1>
        <p class="mt-3 text-white/75">Kalender kegiatan dan daftar agenda madrasah.</p>
    </div>
</div>

<section class="site-container py-12">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <h2 class="font-display text-2xl font-extrabold text-kemenag-deep">
            {{ $cursor->translatedFormat('F Y') }}
        </h2>
        <div class="flex gap-2">
            <a href="{{ route('agendas.index', ['bulan' => $prev->month, 'tahun' => $prev->year]) }}" class="rounded-md border border-kemenag/20 bg-white px-3 py-2 text-sm font-bold text-kemenag-deep hover:bg-kemenag-soft">← Sebelumnya</a>
            <a href="{{ route('agendas.index', ['bulan' => $next->month, 'tahun' => $next->year]) }}" class="rounded-md border border-kemenag/20 bg-white px-3 py-2 text-sm font-bold text-kemenag-deep hover:bg-kemenag-soft">Berikutnya →</a>
        </div>
    </div>

    <div class="calendar-grid mb-3 text-center text-[11px] font-bold uppercase tracking-wide text-muted">
        @foreach (['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $day)
            <div>{{ $day }}</div>
        @endforeach
    </div>

    @php
        $startPad = ($cursor->dayOfWeekIso - 1);
        $daysInMonth = $cursor->daysInMonth;
    @endphp

    <div class="calendar-grid">
        @for ($i = 0; $i < $startPad; $i++)
            <div class="min-h-20 rounded-xl bg-transparent"></div>
        @endfor
        @for ($day = 1; $day <= $daysInMonth; $day++)
            @php
                $dateKey = $cursor->copy()->day($day)->format('Y-m-d');
                $dayItems = $monthAgendas->get($dateKey, collect());
                $isToday = $dateKey === now()->format('Y-m-d');
            @endphp
            <div class="min-h-24 rounded-xl border p-2 {{ $isToday ? 'border-kemenag bg-kemenag-soft' : 'border-kemenag/10 bg-white' }}">
                <p class="text-xs font-extrabold {{ $isToday ? 'text-kemenag' : 'text-kemenag-deep' }}">{{ $day }}</p>
                <div class="mt-1 space-y-1">
                    @foreach ($dayItems->take(2) as $item)
                        <p class="truncate rounded bg-kemenag/10 px-1.5 py-0.5 text-[10px] font-semibold text-kemenag-deep" title="{{ $item->title }}">{{ $item->title }}</p>
                    @endforeach
                    @if ($dayItems->count() > 2)
                        <p class="text-[10px] text-muted">+{{ $dayItems->count() - 2 }} lagi</p>
                    @endif
                </div>
            </div>
        @endfor
    </div>

    <div class="mt-12 space-y-4">
        <h2 class="font-display text-2xl font-extrabold text-kemenag-deep">Daftar agenda</h2>
        @forelse ($agendas as $item)
            <article class="rounded-2xl border border-kemenag/10 bg-white p-6 shadow-sm">
                <p class="news-meta">
                    {{ $item->starts_at->translatedFormat('d F Y H:i') }}
                    @if ($item->ends_at) — {{ $item->ends_at->translatedFormat('d F Y H:i') }} @endif
                </p>
                <h3 class="mt-2 font-display text-2xl font-extrabold text-kemenag-deep">{{ $item->title }}</h3>
                @if ($item->location)<p class="mt-2 text-sm text-muted">Lokasi: {{ $item->location }}</p>@endif
                @if ($item->description)<p class="mt-3 text-sm leading-relaxed text-ink/80">{{ $item->description }}</p>@endif
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-kemenag/20 bg-white p-8 text-muted">Belum ada agenda.</p>
        @endforelse
        <div>{{ $agendas->links() }}</div>
    </div>
</section>
@endsection
