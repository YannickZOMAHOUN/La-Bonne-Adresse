{{-- Partial : carte d'un établissement --}}
{{-- Usage : @include('visitor.partials.card', ['etab' => $etab]) --}}

<div class="addr-card">
    <a href="{{ route('adresses.show', $etab->slug) }}" class="addr-img-link">
        <div class="addr-img" style="background-image: url('{{ $etab->photo_url }}')">
            @if(!$etab->photo_principale)
                <span class="addr-img-emoji">{{ $etab->categorie->emoji }}</span>
            @endif
            <span class="addr-badge addr-badge--{{ $etab->categorie->slug }}">
                {{ $etab->categorie->emoji }} {{ $etab->categorie->nom }}
            </span>
            @if($etab->en_vedette)
                <span class="addr-vedette">⭐ Vedette</span>
            @endif
        </div>
    </a>
    <div class="addr-body">
        <div class="addr-ville">📍 {{ $etab->ville->nom }}</div>
        <h3 class="addr-name">
            <a href="{{ route('adresses.show', $etab->slug) }}">{{ $etab->nom }}</a>
        </h3>
        <p class="addr-desc">{{ Str::limit($etab->description, 90) }}</p>

        @if($etab->services->isNotEmpty())
        <div class="addr-tags">
            @foreach($etab->services->take(3) as $service)
                <span class="tag">{{ $service->emoji }} {{ $service->libelle }}</span>
            @endforeach
        </div>
        @endif

        <div class="addr-footer">
            @if($etab->whatsapp)
                <a href="{{ $etab->whatsapp_link }}" target="_blank" class="addr-contact">
                    📱 WhatsApp
                </a>
            @elseif($etab->telephone)
                <a href="tel:{{ $etab->telephone }}" class="addr-contact">
                    📞 {{ $etab->telephone }}
                </a>
            @else
                <span></span>
            @endif
            <a href="{{ route('adresses.show', $etab->slug) }}" class="btn-voir">
                Voir la fiche →
            </a>
        </div>
    </div>
</div>
