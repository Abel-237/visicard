<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'platform_name' => config('app.name'),
            'platform_logo' => config('app.logo'),
            'platform_description' => config('app.description'),
            'contact_email' => config('app.contact_email'),
            'enable_registration' => config('app.enable_registration', true),
            'enable_business_cards' => config('app.enable_business_cards', true),
            'enable_events' => config('app.enable_events', true),
            'enable_qr_codes' => config('app.enable_qr_codes', true),
            'enable_nfc' => config('app.enable_nfc', false),
            'max_business_cards' => config('app.max_business_cards', 5),
            'max_events' => config('app.max_events', 10),
            'theme_color' => config('app.theme_color', '#3498db'),
            'theme_secondary_color' => config('app.theme_secondary_color', '#2ecc71'),
            'maintenance_mode' => config('app.maintenance_mode', false),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_description' => 'nullable|string',
            'contact_email' => 'required|email',
            'enable_registration' => 'boolean',
            'enable_business_cards' => 'boolean',
            'enable_events' => 'boolean',
            'enable_qr_codes' => 'boolean',
            'enable_nfc' => 'boolean',
            'max_business_cards' => 'required|integer|min:1',
            'max_events' => 'required|integer|min:1',
            'theme_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'theme_secondary_color' => 'required|regex:/^#[a-fA-F0-9]{6}$/',
            'maintenance_mode' => 'boolean',
            'platform_logo' => 'nullable|image|max:2048',
        ]);

        // Gérer le téléchargement du logo
        if ($request->hasFile('platform_logo')) {
            $logo = $request->file('platform_logo');
            $logoPath = $logo->store('public/logos');
            $validated['platform_logo'] = Storage::url($logoPath);
        }

        // Mettre à jour les paramètres dans le fichier de configuration
        foreach ($validated as $key => $value) {
            config(["app.{$key}" => $value]);
        }

        // Vider le cache des paramètres
        Cache::forget('app_settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Les paramètres ont été mis à jour avec succès.');
    }

    public function toggleMaintenanceMode()
    {
        $maintenanceMode = !config('app.maintenance_mode');
        config(['app.maintenance_mode' => $maintenanceMode]);
        Cache::forget('app_settings');

        return redirect()->back()
            ->with('success', $maintenanceMode 
                ? 'Le mode maintenance a été activé.' 
                : 'Le mode maintenance a été désactivé.');
    }

    public function clearCache()
    {
        Cache::flush();
        return redirect()->back()
            ->with('success', 'Le cache a été vidé avec succès.');
    }
} 