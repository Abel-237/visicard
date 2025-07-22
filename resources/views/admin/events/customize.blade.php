@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">Personnalisation de l'événement : {{ $event->title }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personnalisation de l'interface</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.customize', $event->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Logo -->
                        <div class="form-group">
                            <label for="logo">Logo de l'événement</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="logo" name="logo">
                                <label class="custom-file-label" for="logo">Choisir un fichier</label>
                            </div>
                            @if($event->logo_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($event->logo_path) }}" alt="Logo actuel" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>

                        <!-- Image de fond -->
                        <div class="form-group">
                            <label for="background_image">Image de fond</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="background_image" name="background_image">
                                <label class="custom-file-label" for="background_image">Choisir un fichier</label>
                            </div>
                            @if($event->background_image_path)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($event->background_image_path) }}" alt="Image de fond actuelle" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>

                        <!-- Couleur du thème -->
                        <div class="form-group">
                            <label for="theme_color">Couleur du thème</label>
                            <input type="color" class="form-control" id="theme_color" name="theme_color" value="{{ $event->theme_color ?? '#4e73df' }}">
                        </div>

                        <!-- CSS personnalisé -->
                        <div class="form-group">
                            <label for="custom_css">CSS personnalisé</label>
                            <textarea class="form-control" id="custom_css" name="custom_css" rows="5">{{ $event->custom_css }}</textarea>
                            <small class="form-text text-muted">Ajoutez vos styles CSS personnalisés ici.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Aperçu -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aperçu</h6>
                </div>
                <div class="card-body">
                    <div id="preview" class="border p-3" style="min-height: 300px;">
                        <!-- L'aperçu sera mis à jour en temps réel via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aide</h6>
                </div>
                <div class="card-body">
                    <h6>Conseils pour la personnalisation :</h6>
                    <ul class="small">
                        <li>Utilisez des images de haute qualité pour le logo et l'arrière-plan</li>
                        <li>La couleur du thème sera utilisée pour les éléments principaux de l'interface</li>
                        <li>Le CSS personnalisé vous permet d'ajuster finement l'apparence</li>
                        <li>Testez votre interface sur différents appareils</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour du nom du fichier sélectionné
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Mise à jour de l'aperçu en temps réel
    function updatePreview() {
        const themeColor = $('#theme_color').val();
        const customCss = $('#custom_css').val();
        
        $('#preview').css({
            'background-color': themeColor + '10',
            'border-color': themeColor
        });
        
        // Appliquer le CSS personnalisé
        let styleTag = $('#preview-style');
        if (styleTag.length === 0) {
            styleTag = $('<style id="preview-style"></style>').appendTo('head');
        }
        styleTag.html(customCss);
    }

    $('#theme_color, #custom_css').on('input', updatePreview);
    updatePreview();
</script>
@endpush 