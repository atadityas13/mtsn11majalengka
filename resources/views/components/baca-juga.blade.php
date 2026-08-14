<aside class="baca-juga" aria-label="Baca juga">
    <p class="baca-juga__label">Baca juga</p>
    <a href="{{ route('posts.show', $post->slug) }}" class="baca-juga__link">
        {{ $post->title }}
    </a>
</aside>
