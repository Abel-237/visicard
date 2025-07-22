<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Récupérer toutes les business cards avec des logos dans l'ancien format
        $businessCards = DB::table('business_cards')
            ->whereNotNull('logo')
            ->where('logo', 'like', 'logos/%')
            ->get();

        foreach ($businessCards as $card) {
            $oldPath = $card->logo;
            $newPath = 'business-cards/' . $oldPath;
            
            // Vérifier si le fichier existe dans l'ancien emplacement
            if (Storage::disk('public')->exists($oldPath)) {
                // Déplacer le fichier vers le nouvel emplacement
                $content = Storage::disk('public')->get($oldPath);
                Storage::disk('public')->put($newPath, $content);
                
                // Supprimer l'ancien fichier
                Storage::disk('public')->delete($oldPath);
                
                // Mettre à jour le chemin dans la base de données
                DB::table('business_cards')
                    ->where('id', $card->id)
                    ->update(['logo' => $newPath]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Récupérer toutes les business cards avec des logos dans le nouveau format
        $businessCards = DB::table('business_cards')
            ->whereNotNull('logo')
            ->where('logo', 'like', 'business-cards/logos/%')
            ->get();

        foreach ($businessCards as $card) {
            $newPath = $card->logo;
            $oldPath = str_replace('business-cards/', '', $newPath);
            
            // Vérifier si le fichier existe dans le nouvel emplacement
            if (Storage::disk('public')->exists($newPath)) {
                // Déplacer le fichier vers l'ancien emplacement
                $content = Storage::disk('public')->get($newPath);
                Storage::disk('public')->put($oldPath, $content);
                
                // Supprimer le nouveau fichier
                Storage::disk('public')->delete($newPath);
                
                // Mettre à jour le chemin dans la base de données
                DB::table('business_cards')
                    ->where('id', $card->id)
                    ->update(['logo' => $oldPath]);
            }
        }
    }
};
