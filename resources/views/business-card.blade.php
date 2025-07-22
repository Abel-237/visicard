@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="bg-white rounded-xl shadow-lg p-8 max-w-md w-full">
        {{-- Logo --}}
        @if($businessCard->logo)
            <div class="flex justify-center mb-4">
                <img src="{{ asset('storage/' . $businessCard->logo) }}" alt="Logo" class="h-20 w-20 object-cover rounded-full border-2 border-gray-200 shadow">
            </div>
        @endif

        {{-- Nom et poste --}}
        <div class="text-center">
            <h1 class="text-2xl font-bold text-gray-800">{{ $businessCard->name }}</h1>
            <p class="text-gray-500">{{ $businessCard->position }} @ {{ $businessCard->company }}</p>
            <p class="text-sm text-gray-400">{{ $businessCard->industry }}</p>
        </div>

        {{-- Coordonnées --}}
        <div class="mt-6 space-y-2">
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" ...> <!-- Icône email --> </svg>
                <a href="mailto:{{ $businessCard->email }}" class="hover:underline">{{ $businessCard->email }}</a>
            </div>
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" ...> <!-- Icône téléphone --> </svg>
                <a href="tel:{{ $businessCard->phone }}" class="hover:underline">{{ $businessCard->phone }}</a>
            </div>
            @if($businessCard->website)
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" ...> <!-- Icône site web --> </svg>
                <a href="{{ $businessCard->website }}" target="_blank" class="hover:underline">{{ $businessCard->website }}</a>
            </div>
            @endif
            @if($businessCard->address)
            <div class="flex items-center text-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" ...> <!-- Icône adresse --> </svg>
                <span>{{ $businessCard->address }}</span>
            </div>
            @endif
        </div>

        {{-- Bio --}}
        @if($businessCard->bio)
        <div class="mt-6">
            <p class="text-gray-700 italic">"{{ $businessCard->bio }}"</p>
        </div>
        @endif

        {{-- Réseaux sociaux --}}
        @if($businessCard->social_media)
        <div class="mt-6 flex justify-center space-x-4">
            @foreach($businessCard->social_media as $platform => $url)
                <a href="{{ $url }}" target="_blank" class="text-gray-500 hover:text-blue-600">
                    {{-- Icône dynamique selon $platform --}}
                    @if($platform === 'linkedin')
                        <svg ...> <!-- Icône LinkedIn --> </svg>
                    @elseif($platform === 'twitter')
                        <svg ...> <!-- Icône Twitter --> </svg>
                    @elseif($platform === 'facebook')
                        <svg ...> <!-- Icône Facebook --> </svg>
                    @else
                        <svg ...> <!-- Icône générique --> </svg>
                    @endif
                </a>
            @endforeach
        </div>
        @endif

        {{-- Bouton de partage --}}
        <div class="mt-8 flex justify-center">
            <a href="{{ $businessCard->getPublicShareUrl() }}" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow hover:bg-blue-700 transition">
                Partager ma carte
            </a>
        </div>
    </div>
</div>
@endsection 