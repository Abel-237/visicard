<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .event-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .event-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }
        .event-category {
            display: inline-block;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .event-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .event-content {
            margin-bottom: 30px;
            line-height: 1.5;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .tag {
            display: inline-block;
            background-color: #f8f9fa;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .comment {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .comment-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="event-title">{{ $event->title }}</div>
        <div class="event-meta">
            Publié le {{ $event->published_at->format('d/m/Y') }} par {{ $event->user->name }}
        </div>
        <div class="event-category">
            Catégorie: {{ $event->category->name }}
        </div>
    </div>
    
    <div class="event-info">
        @if($event->event_date)
            <div><strong>Date:</strong> {{ $event->event_date->format('d/m/Y à H:i') }}</div>
        @endif
        @if($event->location)
            <div><strong>Lieu:</strong> {{ $event->location }}</div>
        @endif
        <div><strong>Vues:</strong> {{ $event->views }}</div>
        <div><strong>Commentaires:</strong> {{ $event->comments->count() }}</div>
    </div>
    
    <div class="event-content">
        {!! nl2br(e($event->content)) !!}
    </div>
    
    @if($event->tags->count() > 0)
        <div class="section-title">Tags</div>
        <div>
            @foreach($event->tags as $tag)
                <span class="tag">#{{ $tag->name }}</span>
            @endforeach
        </div>
    @endif
    
    @if($event->comments->count() > 0)
        <div class="section-title">Commentaires ({{ $event->comments->count() }})</div>
        @foreach($event->comments as $comment)
            <div class="comment">
                <div class="comment-meta">
                    <strong>{{ $comment->user->name }}</strong> - {{ $comment->created_at->format('d/m/Y H:i') }}
                </div>
                {{ $comment->content }}
            </div>
        @endforeach
    @endif
    
    <div class="footer">
        Document généré le {{ now()->format('d/m/Y à H:i') }} | Tous droits réservés
    </div>
</body>
</html> 