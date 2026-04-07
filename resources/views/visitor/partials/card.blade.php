<div class="addr-card">
    <a href="{{ route('adresses.show', $etab->slug) }}" class="addr-img-link">
        <div class="addr-img" @if($etab->photo_principale) style="background-image: url('{{ asset('storage/'.$etab->photo_principale) }}');" @endif>
            @if(!$etab->photo_principale)
                <span class="addr-img-emoji">{{ $etab->categorie->emoji ?? '🏪' }}</span>
            @endif
            <span class="addr-badge addr-badge--{{ Str::slug($etab->categorie->nom ?? '') }}">
                {{ $etab->categorie->nom ?? 'Établissement' }}
            </span>
            @if($etab->vedette)
                <span class="addr-vedette">⭐ En vedette</span>
            @endif
        </div>
    </a>

    <div class="addr-body">
        <div class="addr-ville">
            📍 {{ $etab->ville->nom ?? 'Bénin' }}
        </div>

        <h3 class="addr-name">
            <a href="{{ route('adresses.show', $etab->slug) }}">{{ $etab->nom }}</a>
        </h3>

        @if($etab->description_courte)
            <p class="addr-desc">{{ Str::limit($etab->description_courte, 100) }}</p>
        @endif

        @if($etab->services->count())
            <div class="addr-tags">
                @foreach($etab->services->take(3) as $service)
                    <span class="tag">{{ $service->nom }}</span>
                @endforeach
                @if($etab->services->count() > 3)
                    <span class="tag">+{{ $etab->services->count() - 3 }}</span>
                @endif
            </div>
        @endif

        <div class="addr-footer">
            @if($etab->telephone)
                <a href="tel:{{ $etab->telephone }}" class="addr-contact">
                    📞 {{ $etab->telephone }}
                </a>
            @endif
            <a href="{{ route('adresses.show', $etab->slug) }}" class="btn-voir">
                Voir détails →
            </a>
        </div>
    </div>
</div>v
