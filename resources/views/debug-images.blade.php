@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1>Debug - Affichage des images</h1>
            
            <div class="alert alert-info">
                <h5>Informations de debug :</h5>
                <ul>
                    <li>App Debug: {{ config('app.debug') ? 'ON' : 'OFF' }}</li>
                    <li>Storage Link: {{ file_exists(public_path('storage')) ? 'EXISTS' : 'MISSING' }}</li>
                    <li>Storage Path: {{ storage_path('app/public') }}</li>
                    <li>Public Storage Path: {{ public_path('storage') }}</li>
                </ul>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h3>Test 1: Image existante</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Chemin: business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg</h5>
                            
                            <h6>Debug Info:</h6>
                            {!! \App\Helpers\ImageHelper::debugImage('business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg') !!}
                            
                            <h6>Affichage:</h6>
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                'business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg',
                                'Test Image',
                                'mb-3',
                                ['style' => 'width: 100px; height: 100px;']
                            ) !!}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h3>Test 2: Image inexistante</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Chemin: image-inexistante.jpg</h5>
                            
                            <h6>Debug Info:</h6>
                            {!! \App\Helpers\ImageHelper::debugImage('image-inexistante.jpg') !!}
                            
                            <h6>Affichage:</h6>
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                'image-inexistante.jpg',
                                'Test Image',
                                'mb-3',
                                ['style' => 'width: 100px; height: 100px;']
                            ) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h3>Test 3: Cartes de visite de la base de données</h3>
                    @php
                        $businessCards = \App\Models\BusinessCard::with('user')->take(3)->get();
                    @endphp
                    
                    @if($businessCards->count() > 0)
                        @foreach($businessCards as $card)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5>{{ $card->name }} (ID: {{ $card->id }})</h5>
                                    <p><strong>Logo path:</strong> {{ $card->logo ?? 'NULL' }}</p>
                                    
                                    <h6>Debug Info:</h6>
                                    {!! \App\Helpers\ImageHelper::debugImage($card->logo) !!}
                                    
                                    <h6>Affichage:</h6>
                                    <div class="d-flex align-items-center">
                                        {!! \App\Helpers\ImageHelper::displayProfileImage(
                                            $card->logo,
                                            $card->name,
                                            'me-3',
                                            ['style' => 'width: 80px; height: 80px;']
                                        ) !!}
                                        <div>
                                            <p class="mb-1"><strong>{{ $card->name }}</strong></p>
                                            <p class="mb-0 text-muted">{{ $card->position }} chez {{ $card->company }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-warning">
                            Aucune carte de visite trouvée dans la base de données.
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h3>Test 4: Méthodes alternatives</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Test avec asset() direct:</h5>
                            <img src="{{ asset('storage/business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg') }}" 
                                 alt="Test direct" 
                                 style="width: 100px; height: 100px; object-fit: cover;"
                                 class="rounded-circle">
                            
                            <h5 class="mt-3">Test avec Storage::url():</h5>
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url('business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg') }}" 
                                 alt="Test Storage::url" 
                                 style="width: 100px; height: 100px; object-fit: cover;"
                                 class="rounded-circle">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 