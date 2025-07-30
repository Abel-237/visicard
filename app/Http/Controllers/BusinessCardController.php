<?php

namespace App\Http\Controllers;

use App\Models\BusinessCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BusinessCardController extends Controller
{
    public function index()
    {
        $businessCards = BusinessCard::with('user')
            ->when(request('search'), function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('industry', 'like', "%{$search}%");
                });
            })
            ->when(request('industry'), function($query, $industry) {
                $query->where('industry', $industry);
            })
            ->latest()
            ->paginate(12);

        $industries = BusinessCard::distinct()->pluck('industry');

        return view('business-cards.index', compact('businessCards', 'industries'));
    }

    public function create()
    {
        // Suppression du blocage : l'utilisateur peut toujours accéder au formulaire
        return view('business-cards.create');
    }

    public function store(Request $request)
    {
        // Si une carte existe déjà, on la supprime avant d'en créer une nouvelle
        $user = auth()->user();
        if ($user->businessCard) {
            $user->businessCard->delete();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'industry' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'social_media' => 'nullable|array',
            'social_media.*' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'visibility' => 'required|in:public,private',
        ]);

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('business-cards/logos', 'public');
            $validated['logo'] = $logoPath;
        }

        if (isset($validated['social_media'])) {
            $validated['social_media'] = json_encode($validated['social_media']);
        }

        $validated['visibility'] = $request->input('visibility', 'private');
        $validated['user_id'] = $user->id;

        $businessCard = BusinessCard::create($validated);

        return redirect()->route('business-cards.show', $businessCard)
            ->with('success', 'Carte de visite créée avec succès.');
    }

    public function show(BusinessCard $businessCard)
    {
        $businessCard->increment('views');
        $businessCard->load('user');

        // Générer l'URL publique de la carte
        $publicUrl = route('shared.card', ['token' => $businessCard->user->username ?? $businessCard->id]);

        // Générer le QR code
        $qrCode = QrCode::size(200)->generate($publicUrl);

        return view('business-cards.show', compact('businessCard', 'qrCode'));
    }

    public function edit(BusinessCard $businessCard)
    {
        $this->authorize('update', $businessCard);
        $publicUrl = route('shared.card', ['token' => $businessCard->user->username ?? $businessCard->id]);
        $qrCode = QrCode::size(200)->generate($publicUrl);
        return view('business-cards.edit', compact('businessCard', 'qrCode'));
    }

    public function update(Request $request, BusinessCard $businessCard)
    {
        $this->authorize('update', $businessCard);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'industry' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:2048',
            'social_media' => 'nullable|array',
            'social_media.*' => 'nullable|url|max:255',
            'visibility' => 'required|in:public,private',
        ]);

        if ($request->hasFile('logo')) {
            if ($businessCard->logo) {
                Storage::disk('public')->delete($businessCard->logo);
            }
            $validated['logo'] = $request->file('logo')->store('business-cards/logos', 'public');
        }

        $validated['social_media'] = json_encode($validated['social_media'] ?? []);
        $validated['visibility'] = $request->input('visibility', 'private');

        $businessCard->update($validated);

        return redirect()->route('business-cards.show', $businessCard)
            ->with('success', 'Carte de visite mise à jour avec succès.');
    }

    public function destroy(BusinessCard $businessCard)
    {
        $this->authorize('delete', $businessCard);

        if ($businessCard->logo) {
            Storage::disk('public')->delete($businessCard->logo);
        }

        $businessCard->delete();

        return redirect()->route('business-cards.index')
            ->with('success', 'Votre carte de visite a été supprimée avec succès !');
    }

    // Génération vCard
    public function vcard(BusinessCard $businessCard)
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\n";
        $vcard .= "FN:" . $businessCard->name . "\n";
        $vcard .= "ORG:" . $businessCard->company . "\n";
        $vcard .= "TITLE:" . ($businessCard->title ?? $businessCard->position) . "\n";
        $vcard .= "TEL;TYPE=WORK,VOICE:" . $businessCard->phone . "\n";
        $vcard .= "EMAIL;TYPE=PREF,INTERNET:" . $businessCard->email . "\n";
        if ($businessCard->website) $vcard .= "URL:" . $businessCard->website . "\n";
        if ($businessCard->address) $vcard .= "ADR;TYPE=WORK:" . $businessCard->address . "\n";
        $vcard .= "END:VCARD\n";
        return response($vcard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', 'attachment; filename=business-card.vcf');
    }
} 