@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>{{ __('app.welcome') }}, {{ Auth::user()->name }}!</h1>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('app.dashboard') }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ __('app.events_created') }}: {{ $eventCount }}</p>
                    <p>{{ __('app.unread_notifications') }}: {{ $unreadNotifications }}</p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('app.language') }}: {{ \App\Helpers\TranslationHelper::getCurrentLanguageName() }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ __('app.select_language') }}:</p>
                    <div class="btn-group" role="group">
                        @foreach(\App\Helpers\TranslationHelper::getAvailableLanguages() as $locale => $name)
                            <a href="{{ route('language.switch', $locale) }}" 
                               class="btn btn-outline-primary {{ app()->getLocale() == $locale ? 'active' : '' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 