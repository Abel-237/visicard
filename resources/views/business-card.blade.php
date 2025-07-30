@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto">
            <!-- Carte de visite principale -->
            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden transform hover:scale-105 transition-all duration-300">
                <!-- En-tête avec gradient -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
        @if($businessCard->logo)
                            <div class="flex justify-center mb-6">
                                <div class="bg-white p-3 rounded-full shadow-lg">
                                    <img src="{{ asset('storage/' . $businessCard->logo) }}" 
                                         alt="Logo" 
                                         class="h-16 w-16 object-cover rounded-full">
                                </div>
            </div>
        @endif

        <div class="text-center">
                            <h1 class="text-3xl font-bold mb-2">{{ $businessCard->name }}</h1>
                            <p class="text-xl opacity-90 mb-1">{{ $businessCard->position }}</p>
                            <p class="text-lg opacity-80">@ {{ $businessCard->company }}</p>
                            @if($businessCard->industry)
                                <div class="mt-3">
                                    <span class="bg-white bg-opacity-20 px-4 py-1 rounded-full text-sm">
                                        {{ $businessCard->industry }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
        </div>

                <!-- Contenu principal -->
                <div class="p-8">
                    <!-- Coordonnées -->
                    <div class="space-y-4 mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-address-card text-blue-600 mr-2"></i>
                            Coordonnées
                        </h3>
                        
                        <div class="grid gap-4">
                            @if($businessCard->email)
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-blue-50 transition-colors">
                                <div class="bg-blue-100 p-2 rounded-lg mr-4">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Email</p>
                                    <a href="mailto:{{ $businessCard->email }}" 
                                       class="text-gray-800 font-medium hover:text-blue-600 transition-colors">
                                        {{ $businessCard->email }}
                                    </a>
                                </div>
                                <button onclick="copyToClipboard('{{ $businessCard->email }}')" 
                                        class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                            @endif

                            @if($businessCard->phone)
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-green-50 transition-colors">
                                <div class="bg-green-100 p-2 rounded-lg mr-4">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Téléphone</p>
                                    <a href="tel:{{ $businessCard->phone }}" 
                                       class="text-gray-800 font-medium hover:text-green-600 transition-colors">
                                        {{ $businessCard->phone }}
                                    </a>
            </div>
                                <button onclick="copyToClipboard('{{ $businessCard->phone }}')" 
                                        class="text-gray-400 hover:text-green-600 transition-colors">
                                    <i class="fas fa-copy"></i>
                                </button>
            </div>
                            @endif

            @if($businessCard->website)
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-purple-50 transition-colors">
                                <div class="bg-purple-100 p-2 rounded-lg mr-4">
                                    <i class="fas fa-globe text-purple-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Site web</p>
                                    <a href="{{ $businessCard->website }}" 
                                       target="_blank" 
                                       class="text-gray-800 font-medium hover:text-purple-600 transition-colors">
                                        {{ $businessCard->website }}
                                    </a>
                                </div>
                                <i class="fas fa-external-link-alt text-gray-400"></i>
            </div>
            @endif

            @if($businessCard->address)
                            <div class="flex items-center p-3 bg-gray-50 rounded-xl hover:bg-orange-50 transition-colors">
                                <div class="bg-orange-100 p-2 rounded-lg mr-4">
                                    <i class="fas fa-map-marker-alt text-orange-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-500">Adresse</p>
                                    <span class="text-gray-800 font-medium">{{ $businessCard->address }}</span>
                                </div>
            </div>
            @endif
                        </div>
        </div>

                    <!-- Bio -->
        @if($businessCard->bio)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-quote-left text-blue-600 mr-2"></i>
                            À propos
                        </h3>
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-6 rounded-2xl border-l-4 border-blue-600">
                            <p class="text-gray-700 italic text-lg leading-relaxed">
                                "{{ $businessCard->bio }}"
                            </p>
                        </div>
        </div>
        @endif

                    <!-- Réseaux sociaux -->
                    @if($businessCard->social_media && count($businessCard->social_media) > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-share-alt text-blue-600 mr-2"></i>
                            Réseaux sociaux
                        </h3>
                        <div class="flex flex-wrap gap-3">
            @foreach($businessCard->social_media as $platform => $url)
                                <a href="{{ $url }}" 
                                   target="_blank" 
                                   class="flex items-center px-4 py-3 bg-gray-100 rounded-xl hover:bg-blue-100 transition-all duration-200 group">
                                    <i class="fab fa-{{ strtolower($platform) }} text-2xl mr-3 
                                        @if($platform === 'linkedin') text-blue-600
                                        @elseif($platform === 'twitter') text-blue-400
                                        @elseif($platform === 'facebook') text-blue-700
                                        @elseif($platform === 'instagram') text-pink-600
                                        @else text-gray-600
                                        @endif"></i>
                                    <span class="font-medium text-gray-700 group-hover:text-blue-600 transition-colors">
                                        {{ ucfirst($platform) }}
                                    </span>
                </a>
            @endforeach
                        </div>
        </div>
        @endif

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ $businessCard->getPublicShareUrl() }}" 
                           class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-xl font-semibold text-center hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-share mr-2"></i>
                Partager ma carte
            </a>
                        
                        <button onclick="downloadCard()" 
                                class="flex-1 bg-gray-100 text-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-200 transition-all duration-200 border border-gray-300">
                            <i class="fas fa-download mr-2"></i>
                            Télécharger
                        </button>
                    </div>
                </div>
            </div>

            <!-- Message de succès pour la copie -->
            <div id="copySuccess" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 z-50">
                <i class="fas fa-check mr-2"></i>
                Copié dans le presse-papiers !
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const success = document.getElementById('copySuccess');
        success.classList.remove('translate-x-full');
        setTimeout(() => {
            success.classList.add('translate-x-full');
        }, 2000);
    });
}

function downloadCard() {
    // Fonction pour télécharger la carte (peut être implémentée plus tard)
    alert('Fonctionnalité de téléchargement en cours de développement');
}
</script>
@endpush

@push('styles')
<style>
    .hover\:scale-105:hover {
        transform: scale(1.02);
    }
    
    @media (max-width: 640px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
    }
</style>
@endpush
@endsection 