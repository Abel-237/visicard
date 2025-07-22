@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h1>Test d'affichage des images</h1>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h3>Test avec ImageHelper</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Avec image existante :</h5>
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                'business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg',
                                'Test Image',
                                'mb-3',
                                ['style' => 'width: 100px; height: 100px;']
                            ) !!}
                            
                            <h5>Avec image inexistante :</h5>
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                'image-inexistante.jpg',
                                'Test Image',
                                'mb-3',
                                ['style' => 'width: 100px; height: 100px;']
                            ) !!}
                            
                            <h5>Avec chemin null :</h5>
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                null,
                                'Test Image',
                                'mb-3',
                                ['style' => 'width: 100px; height: 100px;']
                            ) !!}
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <h3>Test avec directives Blade</h3>
                    <div class="card">
                        <div class="card-body">
                            <h5>Avec @profileImage :</h5>
                            @profileImage('business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg', 'Test Image', 'mb-3', ['style' => 'width: 100px; height: 100px;'])
                            
                            <h5>Avec @image :</h5>
                            @image('business-cards/logos/YPHMc0aLOZiIjyISeqeeo9L1mOMrtxYxQHIPlDZh.jpg', 'Test Image', 'img-thumbnail', ['style' => 'width: 100px; height: 100px;'])
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-12">
                    <h3>Test avec les cartes de visite existantes</h3>
                    @php
                        $businessCards = \App\Models\BusinessCard::with('user')->take(3)->get();
                    @endphp
                    
                    @foreach($businessCards as $card)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    {!! \App\Helpers\ImageHelper::displayProfileImage(
                                        $card->logo,
                                        $card->name,
                                        'me-3',
                                        ['style' => 'width: 80px; height: 80px;']
                                    ) !!}
                                    <div>
                                        <h5>{{ $card->name }}</h5>
                                        <p class="text-muted">{{ $card->position }} chez {{ $card->company }}</p>
                                        <small class="text-muted">Logo path: {{ $card->logo ?? 'Aucun logo' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 