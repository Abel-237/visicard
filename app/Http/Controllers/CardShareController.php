<?php

namespace App\Http\Controllers;

use App\Models\BusinessCard;
use App\Models\CardShare;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\CardShareMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CardShareController extends Controller
{
    /**
     * Show the share interface for a business card
     */
    public function showShareInterface(BusinessCard $businessCard)
    {
        // Ensure user owns this business card
        if ($businessCard->user_id !== Auth::id()) {
            abort(403);
        }

        // Generate username if not exists
        if (!$businessCard->user->username) {
            $username = User::generateUsername($businessCard->user->name);
            $businessCard->user->update(['username' => $username]);
        }

        $publicUrl = $businessCard->getPublicShareUrl();
        $qrCode = QrCode::size(200)->generate($publicUrl);

        return view('business-cards.share', compact('businessCard', 'publicUrl', 'qrCode'));
    }

    /**
     * Share card via email
     */
    public function shareViaEmail(Request $request, BusinessCard $businessCard)
    {
        $validator = Validator::make($request->all(), [
            'recipient_email' => 'required|email',
            'recipient_name' => 'nullable|string|max:255',
            'custom_message' => 'nullable|string|max:1000',
            'subject' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create share record
        $share = CardShare::create([
            'business_card_id' => $businessCard->id,
            'share_token' => CardShare::generateShareToken(),
            'share_method' => 'email',
            'recipient_email' => $request->recipient_email,
            'recipient_name' => $request->recipient_name,
            'custom_message' => $request->custom_message,
            'shared_at' => now(),
        ]);

        // Send email
        $this->sendShareEmail($share, $request->subject);

        return response()->json([
            'success' => true,
            'message' => 'Carte partagée avec succès par email',
            'share' => $share
        ]);
    }

    /**
     * Share card via WhatsApp
     */
    public function shareViaWhatsApp(Request $request, BusinessCard $businessCard)
    {
        $validator = Validator::make($request->all(), [
            'custom_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create share record
        $share = CardShare::create([
            'business_card_id' => $businessCard->id,
            'share_token' => CardShare::generateShareToken(),
            'share_method' => 'whatsapp',
            'custom_message' => $request->custom_message,
            'shared_at' => now(),
        ]);

        // Generate WhatsApp URL
        $message = $request->custom_message ?: "Voici ma carte de visite virtuelle :";
        $whatsappUrl = $this->generateWhatsAppUrl($share->getShareUrl(), $message);

        return response()->json([
            'success' => true,
            'message' => 'Lien WhatsApp généré avec succès',
            'whatsapp_url' => $whatsappUrl,
            'share' => $share
        ]);
    }

    /**
     * Generate QR code for sharing
     */
    public function generateQrCode(BusinessCard $businessCard)
    {
        // Ensure user owns this business card
        if ($businessCard->user_id !== Auth::id()) {
            abort(403);
        }

        $publicUrl = $businessCard->getPublicShareUrl();
        $qrCode = QrCode::size(300)->format('png')->generate($publicUrl);

        // Create share record
        $share = CardShare::create([
            'business_card_id' => $businessCard->id,
            'share_token' => CardShare::generateShareToken(),
            'share_method' => 'qr',
            'shared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'qr_code' => base64_encode($qrCode),
            'share_url' => $publicUrl,
            'share' => $share
        ]);
    }

    /**
     * NFC sharing (placeholder for future implementation)
     */
    public function shareViaNfc(Request $request, BusinessCard $businessCard)
    {
        // Create share record
        $share = CardShare::create([
            'business_card_id' => $businessCard->id,
            'share_token' => CardShare::generateShareToken(),
            'share_method' => 'nfc',
            'shared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Données NFC générées avec succès',
            'nfc_data' => $share->getShareUrl(),
            'share' => $share
        ]);
    }

    /**
     * Export NFC data in NDEF format
     */
    public function exportNFC(BusinessCard $businessCard)
    {
        // Ensure user owns this business card
        if ($businessCard->user_id !== Auth::id()) {
            abort(403);
        }

        $shareUrl = $businessCard->getPublicShareUrl();
        
        // Generate NDEF format data
        $ndefData = $this->generateNDEFData($businessCard, $shareUrl);

        return response()->json([
            'success' => true,
            'nfc_data' => $ndefData,
            'share_url' => $shareUrl
        ]);
    }

    /**
     * Generate NDEF format data for NFC
     */
    private function generateNDEFData(BusinessCard $businessCard, $shareUrl)
    {
        $user = $businessCard->user;
        
        // Create vCard format
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";
        $vcard .= "FN:{$user->name}\r\n";
        
        if ($businessCard->position) {
            $vcard .= "TITLE:{$businessCard->position}\r\n";
        }
        
        if ($businessCard->company) {
            $vcard .= "ORG:{$businessCard->company}\r\n";
        }
        
        if ($businessCard->email) {
            $vcard .= "EMAIL:{$businessCard->email}\r\n";
        }
        
        if ($businessCard->phone) {
            $vcard .= "TEL:{$businessCard->phone}\r\n";
        }
        
        if ($businessCard->website) {
            $vcard .= "URL:{$businessCard->website}\r\n";
        }
        
        if ($businessCard->address) {
            $vcard .= "ADR:;;{$businessCard->address}\r\n";
        }
        
        if ($businessCard->bio) {
            $vcard .= "NOTE:{$businessCard->bio}\r\n";
        }
        
        $vcard .= "URL:{$shareUrl}\r\n";
        $vcard .= "END:VCARD\r\n";
        
        return $vcard;
    }

    /**
     * Public view of a shared card
     */
    public function showSharedCard($token)
    {
        // Try to find by share token first
        $share = CardShare::where('share_token', $token)->first();
        
        if ($share) {
            $businessCard = $share->businessCard;
            $share->markAsViewed(request()->ip(), request()->userAgent());
        } else {
            // Try to find by username
            $user = User::where('username', $token)->first();
            if (!$user || !$user->businessCard) {
                abort(404, 'Carte de visite non trouvée');
            }
            $businessCard = $user->businessCard;
            $share = null;
        }

        return view('business-cards.shared', compact('businessCard', 'share'));
    }

    /**
     * Get sharing statistics
     */
    public function getSharingStats(BusinessCard $businessCard)
    {
        // Ensure user owns this business card
        if ($businessCard->user_id !== Auth::id()) {
            abort(403);
        }

        $stats = [
            'total_shares' => $businessCard->cardShares()->count(),
            'email_shares' => $businessCard->cardShares()->where('share_method', 'email')->count(),
            'whatsapp_shares' => $businessCard->cardShares()->where('share_method', 'whatsapp')->count(),
            'qr_shares' => $businessCard->cardShares()->where('share_method', 'qr')->count(),
            'nfc_shares' => $businessCard->cardShares()->where('share_method', 'nfc')->count(),
            'total_views' => $businessCard->cardShares()->whereNotNull('viewed_at')->count(),
            'recent_shares' => $businessCard->cardShares()
                ->orderBy('shared_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Send share email
     */
    private function sendShareEmail(CardShare $share, $subject = null)
    {
        Mail::to($share->recipient_email)
            ->send(new CardShareMail($share, $share->custom_message, $subject));
    }

    /**
     * Generate WhatsApp sharing URL
     */
    private function generateWhatsAppUrl($url, $message)
    {
        $text = urlencode($message . ' ' . $url);
        return "https://wa.me/?text={$text}";
    }
} 