@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Rapports analytiques</h2>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </div>
                <div class="card-body">
                    <p class="lead">
                        Bienvenue dans la section des rapports analytiques. Cette section vous permet de générer des graphiques et analyses 
                        détaillées sur les événements, la participation des utilisateurs et les tendances de la plateforme.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements par période -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm card-hover">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-4x text-primary mb-3"></i>
                    <h4 class="card-title">Statistiques d'événements par période</h4>
                    <p class="card-text text-muted">
                        Visualisez l'évolution du nombre d'événements créés sur différentes périodes (jour, semaine, mois, année).
                        Filtrez par catégorie et période.
                    </p>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('admin.reports.events-by-period') }}" class="btn btn-primary">
                        <i class="fas fa-eye"></i> Voir le rapport
                    </a>
                </div>
            </div>
        </div>

        <!-- Rapport de participation -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm card-hover">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-4x text-success mb-3"></i>
                    <h4 class="card-title">Rapports de participation</h4>
                    <p class="card-text text-muted">
                        Analysez les événements les plus populaires en termes de vues, commentaires et likes.
                        Identifiez les événements qui suscitent le plus d'intérêt.
                    </p>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('admin.reports.participation') }}" class="btn btn-success">
                        <i class="fas fa-eye"></i> Voir le rapport
                    </a>
                </div>
            </div>
        </div>

        <!-- Analyse des tendances -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm card-hover">
                <div class="card-body text-center">
                    <i class="fas fa-chart-pie fa-4x text-info mb-3"></i>
                    <h4 class="card-title">Analyse des tendances</h4>
                    <p class="card-text text-muted">
                        Découvrez les catégories et tags les plus populaires, les utilisateurs les plus actifs et 
                        les moments privilégiés pour la publication.
                    </p>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="{{ route('admin.reports.trends') }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Voir le rapport
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-lightbulb text-warning"></i> À propos des rapports
                    </h5>
                    <p class="card-text">
                        Ces rapports vous aident à prendre des décisions informées sur la gestion des événements de l'entreprise :
                    </p>
                    <ul>
                        <li>Identifiez les périodes de forte activité pour mieux planifier les nouveaux événements</li>
                        <li>Découvrez quels types d'événements intéressent le plus vos utilisateurs</li>
                        <li>Suivez les tendances et anticipez les besoins de votre audience</li>
                        <li>Identifiez vos utilisateurs les plus engagés</li>
                    </ul>
                    <p class="mb-0">
                        Tous les rapports peuvent être filtrés par période pour affiner votre analyse.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endsection 