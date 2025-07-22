<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() > 0) {
            foreach ($users as $user) {
                // Créer quelques notifications de test pour chaque utilisateur
                Notification::create([
                    'title' => 'Bienvenue sur la plateforme !',
                    'message' => 'Nous sommes ravis de vous accueillir. Commencez par créer votre carte de visite.',
                    'type' => 'welcome',
                    'user_id' => $user->id,
                    'is_read' => false,
                ]);
                
                Notification::create([
                    'title' => 'Nouveau message de John Doe',
                    'message' => 'Bonjour ! J\'ai vu votre carte de visite et j\'aimerais discuter avec vous.',
                    'type' => 'message',
                    'user_id' => $user->id,
                    'is_read' => false,
                ]);
                
                Notification::create([
                    'title' => 'Événement à venir',
                    'message' => 'Un nouvel événement a été créé dans votre région.',
                    'type' => 'event',
                    'user_id' => $user->id,
                    'is_read' => true,
                ]);
            }
        }
    }
} 