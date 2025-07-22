@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Modifier l'événement : {{ $event->title }}</h4>
                </div>

                <div class="card-body">
                    @include('admin.events.form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 