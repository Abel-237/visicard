@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">QR Codes - {{ $event->title }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">QR Codes des participants</h6>
                    <button class="btn btn-primary btn-sm" onclick="printQRCodes()">
                        <i class="fas fa-print"></i> Imprimer tous les QR codes
                    </button>
                </div>
                <div class="card-body">
                    <div class="row" id="qr-codes-container">
                        @foreach($qrCodes as $userId => $qrCode)
                            <div class="col-md-3 col-sm-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" 
                                             alt="QR Code" 
                                             class="img-fluid mb-2">
                                        <h6 class="card-title">{{ $participants->find($userId)->name }}</h6>
                                        <p class="card-text small text-muted">
                                            {{ $participants->find($userId)->email }}
                                        </p>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="printSingleQRCode({{ $userId }})">
                                            <i class="fas fa-print"></i> Imprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Instructions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Pour les participants :</h6>
                            <ul>
                                <li>Présentez votre QR code à l'entrée de l'événement</li>
                                <li>Gardez votre QR code en sécurité</li>
                                <li>Ne partagez pas votre QR code avec d'autres personnes</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Pour les organisateurs :</h6>
                            <ul>
                                <li>Vérifiez chaque QR code à l'entrée</li>
                                <li>Assurez-vous que le nom correspond à la pièce d'identité</li>
                                <li>Marquez les QR codes utilisés pour éviter la réutilisation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Template pour l'impression -->
<div id="print-template" style="display: none;">
    <div class="qr-code-print">
        <img src="" alt="QR Code" class="qr-code-img">
        <div class="participant-info">
            <h4 class="participant-name"></h4>
            <p class="participant-email"></p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .qr-code-print, .qr-code-print * {
            visibility: visible;
        }
        .qr-code-print {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .qr-code-img {
            width: 200px;
            height: 200px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function printQRCodes() {
        const printWindow = window.open('', '_blank');
        const container = document.getElementById('qr-codes-container');
        const template = document.getElementById('print-template');
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>QR Codes - {{ $event->title }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .qr-code-print {
                            text-align: center;
                            margin: 20px;
                            page-break-after: always;
                        }
                        .qr-code-img {
                            width: 200px;
                            height: 200px;
                        }
                        .participant-info {
                            margin-top: 10px;
                        }
                    </style>
                </head>
                <body>
                    ${Array.from(container.children).map(div => {
                        const img = div.querySelector('img');
                        const name = div.querySelector('.card-title').textContent;
                        const email = div.querySelector('.card-text').textContent;
                        return `
                            <div class="qr-code-print">
                                <img src="${img.src}" alt="QR Code" class="qr-code-img">
                                <div class="participant-info">
                                    <h4>${name}</h4>
                                    <p>${email}</p>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }

    function printSingleQRCode(userId) {
        const printWindow = window.open('', '_blank');
        const container = document.querySelector(`[data-user-id="${userId}"]`);
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>QR Code - {{ $event->title }}</title>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        .qr-code-print {
                            text-align: center;
                            margin: 20px;
                        }
                        .qr-code-img {
                            width: 200px;
                            height: 200px;
                        }
                        .participant-info {
                            margin-top: 10px;
                        }
                    </style>
                </head>
                <body>
                    <div class="qr-code-print">
                        <img src="${container.querySelector('img').src}" alt="QR Code" class="qr-code-img">
                        <div class="participant-info">
                            <h4>${container.querySelector('.card-title').textContent}</h4>
                            <p>${container.querySelector('.card-text').textContent}</p>
                        </div>
                    </div>
                </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endpush 