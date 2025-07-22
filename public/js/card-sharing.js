/**
 * Card Sharing Module JavaScript
 * Gère toutes les interactions du module de partage de cartes de visite
 */

class CardSharing {
    constructor() {
        this.initializeEventListeners();
        this.loadSharingStats();
    }

    initializeEventListeners() {
        // Email sharing
        const emailForm = document.getElementById('emailShareForm');
        if (emailForm) {
            emailForm.addEventListener('submit', (e) => this.handleEmailShare(e));
        }

        // WhatsApp sharing
        const whatsappForm = document.getElementById('whatsappShareForm');
        if (whatsappForm) {
            whatsappForm.addEventListener('submit', (e) => this.handleWhatsAppShare(e));
        }

        // Copy to clipboard buttons
        document.querySelectorAll('[data-copy]').forEach(button => {
            button.addEventListener('click', (e) => this.copyToClipboard(e));
        });

        // QR code download
        const qrDownloadBtn = document.getElementById('qrDownloadBtn');
        if (qrDownloadBtn) {
            qrDownloadBtn.addEventListener('click', () => this.downloadQRCode());
        }

        // NFC export
        const nfcExportBtn = document.getElementById('nfcExportBtn');
        if (nfcExportBtn) {
            nfcExportBtn.addEventListener('click', () => this.exportNFC());
        }
    }

    async handleEmailShare(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            this.setLoadingState(submitBtn, true);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const data = await response.json();
            
            if (data.success) {
                this.showToast(data.message, 'success');
                form.reset();
                this.loadSharingStats();
            } else {
                this.showToast('Erreur lors de l\'envoi', 'error');
                if (data.errors) {
                    this.displayFormErrors(form, data.errors);
                }
            }
        } catch (error) {
            this.showToast('Erreur de connexion', 'error');
        } finally {
            this.setLoadingState(submitBtn, false);
        }
    }

    async handleWhatsAppShare(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        
        try {
            this.setLoadingState(submitBtn, true);
            
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });

            const data = await response.json();
            
            if (data.success) {
                window.open(data.whatsapp_url, '_blank');
                this.showToast('WhatsApp ouvert avec le message pré-rempli', 'success');
                form.reset();
                this.loadSharingStats();
            } else {
                this.showToast('Erreur lors de la génération du lien WhatsApp', 'error');
            }
        } catch (error) {
            this.showToast('Erreur de connexion', 'error');
        } finally {
            this.setLoadingState(submitBtn, false);
        }
    }

    async downloadQRCode() {
        const qrContainer = document.querySelector('.qr-code-container');
        if (!qrContainer) return;

        try {
            const response = await fetch(qrContainer.dataset.qrUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                }
            });

            const data = await response.json();
            
            if (data.success) {
                const link = document.createElement('a');
                link.href = 'data:image/png;base64,' + data.qr_code;
                link.download = 'qr-code-carte.png';
                link.click();
                this.showToast('QR Code téléchargé avec succès', 'success');
            } else {
                this.showToast('Erreur lors de la génération du QR Code', 'error');
            }
        } catch (error) {
            this.showToast('Erreur de connexion', 'error');
        }
    }

    async exportNFC() {
        try {
            const response = await fetch('/business-cards/nfc-export', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.downloadNFCData(data.nfc_data, 'carte-nfc.txt');
                this.showToast('Données NFC exportées', 'success');
            } else {
                this.showToast('Erreur lors de l\'export NFC', 'error');
            }
        } catch (error) {
            this.showToast('Erreur de connexion', 'error');
        }
    }

    copyToClipboard(e) {
        const button = e.target;
        const targetId = button.dataset.copy;
        const element = document.getElementById(targetId);
        
        if (!element) return;

        element.select();
        element.setSelectionRange(0, 99999);
        
        try {
            document.execCommand('copy');
            this.showToast('Lien copié dans le presse-papiers !', 'success');
            
            // Change button text temporarily
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copié';
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('btn-success');
            }, 2000);
        } catch (err) {
            this.showToast('Erreur lors de la copie', 'error');
        }
    }

    async loadSharingStats() {
        const statsContainer = document.getElementById('sharingStats');
        if (!statsContainer) return;

        try {
            const response = await fetch(statsContainer.dataset.statsUrl);
            const data = await response.json();
            
            this.updateStatsDisplay(data);
        } catch (error) {
            console.error('Erreur lors du chargement des statistiques:', error);
        }
    }

    updateStatsDisplay(stats) {
        const elements = {
            'totalShares': stats.total_shares,
            'totalViews': stats.total_views,
            'emailShares': stats.email_shares,
            'whatsappShares': stats.whatsapp_shares,
            'qrShares': stats.qr_shares,
            'nfcShares': stats.nfc_shares
        };

        Object.entries(elements).forEach(([id, value]) => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        });
    }

    setLoadingState(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.classList.add('loading');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
        } else {
            button.disabled = false;
            button.classList.remove('loading');
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
    }

    displayFormErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.remove();
        });

        // Display new errors
        Object.entries(errors).forEach(([field, messages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = messages[0];
                input.parentNode.appendChild(feedback);
            }
        });
    }

    showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast show bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} text-white`;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">Notification</strong>
                <button type="button" class="btn-close btn-close-white" onclick="this.parentElement.parentElement.remove()"></button>
            </div>
            <div class="toast-body">${message}</div>
        `;

        toastContainer.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    downloadNFCData(data, filename) {
        const blob = new Blob([data], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        window.URL.revokeObjectURL(url);
    }
}

// NFC Helper Class
class NFCHelper {
    constructor() {
        this.isSupported = 'NDEFReader' in window;
    }

    async writeToNFC(data) {
        if (!this.isSupported) {
            throw new Error('NFC non supporté sur cet appareil');
        }

        try {
            const ndef = new NDEFReader();
            await ndef.write({
                records: [{
                    recordType: "url",
                    data: data
                }]
            });
            return true;
        } catch (error) {
            throw new Error('Erreur lors de l\'écriture NFC: ' + error.message);
        }
    }

    async readFromNFC() {
        if (!this.isSupported) {
            throw new Error('NFC non supporté sur cet appareil');
        }

        try {
            const ndef = new NDEFReader();
            const records = await ndef.read();
            return records;
        } catch (error) {
            throw new Error('Erreur lors de la lecture NFC: ' + error.message);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.cardSharing = new CardSharing();
    window.nfcHelper = new NFCHelper();
});

// Export for use in other scripts
window.CardSharing = CardSharing;
window.NFCHelper = NFCHelper; 