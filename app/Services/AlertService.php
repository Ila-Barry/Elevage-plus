<?php
// app/Services/AlertService.php

namespace App\Services;

use App\Models\User;
use App\Models\Animal;
use App\Models\Produit;
use App\Models\Tache;
use App\Models\Message;
use App\Models\Commentaire;
use App\Models\Publication;
use App\Models\Like;
use App\Models\Share;
use App\Models\Report;
use App\Notifications\{
    WelcomeNotification,
    VaccinationReminderNotification,
    StockCritiqueNotification,
    WeightLossAlertNotification,
    TaskReminderNotification,
    NewMessageNotification,
    NewCommentNotification,
    NewLikeNotification,
    NewShareNotification,
    PublicationReportedNotification,
    ReportResolvedNotification,
    BaseNotification,
    AdminAlertNotification,
};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Service AlertService
 * 
 * Centralise l'envoi de toutes les alertes de la plateforme
 * Utilise les queues Redis pour l'envoi asynchrone
 */
class AlertService
{
    /**
     * Envoie une notification de bienvenue
     */
    public function sendWelcomeAlert(User $user): void
    {
        try {
            // Vérifier que l'utilisateur existe
            if (!$user) {
                Log::error('Tentative d\'envoi de notification à un utilisateur null');
                return;
            }
            
            // Envoyer la notification
            $user->notify(new WelcomeNotification($user));
            
            // Log pour déboguer
            Log::info("Notification de bienvenue envoyée à {$user->email}");
            
        } catch (\Exception $e) {
            Log::error("Erreur envoi notification bienvenue: " . $e->getMessage());
        }
    }

    /**
     * Envoie un rappel de vaccination
     */
    public function sendVaccinationReminder(Animal $animal, Tache $tache): void
    {
        try {
            $user = $animal->elevage->user;
            $user->notify(new VaccinationReminderNotification($animal, $tache));
            Log::info("Rappel vaccination envoyé pour l'animal {$animal->nom}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi rappel vaccination: " . $e->getMessage());
        }
    }

    /**
     * Envoie une alerte stock critique
     */
    public function sendStockCritiqueAlert(Produit $produit): void
    {
        try {
            $user = $produit->elevage->user;
            $user->notify(new StockCritiqueNotification($produit));
            Log::info("Alerte stock critique envoyée pour {$produit->nom}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi alerte stock: " . $e->getMessage());
        }
    }

    /**
     * Envoie une alerte perte de poids
     */
    public function sendWeightLossAlert(Animal $animal, float $poidsAvant, float $poidsApres): void
    {
        try {
            $user = $animal->elevage->user;
            $user->notify(new WeightLossAlertNotification($animal, $poidsAvant, $poidsApres));
            Log::info("Alerte perte de poids envoyée pour l'animal {$animal->nom}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi alerte perte poids: " . $e->getMessage());
        }
    }

    /**
     * Envoie un rappel de tâche
     */
    public function sendTaskReminder(Tache $tache, string $reminderType, string $message): void
    {
        try {
            $user = $tache->user;
            $user->notify(new TaskReminderNotification($tache, $reminderType, $message));
            Log::info("Rappel tâche envoyé pour {$tache->titre} ($reminderType)");
        } catch (\Exception $e) {
            Log::error("Erreur envoi rappel tâche: " . $e->getMessage());
        }
    }

    /**
     * Envoie une notification nouveau message
     */
    public function sendNewMessageAlert(User $destinataire, User $expediteur, Message $message): void
    {
        try {
            $destinataire->notify(new NewMessageNotification($expediteur, $message));
            Log::info("Notification nouveau message envoyée à {$destinataire->email}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi notif nouveau message: " . $e->getMessage());
        }
    }

    /**
     * Envoie une notification nouveau commentaire
     */
    public function sendNewCommentAlert(User $auteurPublication, User $auteurCommentaire, Commentaire $commentaire, Publication $publication): void
    {
        // Ne pas s'envoyer une notification à soi-même
        if ($auteurPublication->id === $auteurCommentaire->id) {
            return;
        }
        
        try {
            $auteurPublication->notify(new NewCommentNotification($auteurCommentaire, $commentaire, $publication));
            Log::info("Notification nouveau commentaire envoyée à {$auteurPublication->email}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi notif nouveau commentaire: " . $e->getMessage());
        }
    }

    /**
     * Envoie une notification nouveau like
     */
    public function sendNewLikeAlert(User $auteurPublication, User $auteurLike, Publication $publication): void
    {
        // Ne pas s'envoyer une notification à soi-même
        if ($auteurPublication->id === $auteurLike->id) {
            return;
        }
        
        try {
            $auteurPublication->notify(new NewLikeNotification($auteurLike, $publication));
            Log::info("Notification nouveau like envoyée à {$auteurPublication->email}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi notif nouveau like: " . $e->getMessage());
        }
    }

    /**
     * Envoie une notification nouveau partage
     */
    public function sendNewShareAlert(User $auteurPublication, User $auteurShare, Publication $publication, string $plateforme): void
    {
        // Ne pas s'envoyer une notification à soi-même
        if ($auteurPublication->id === $auteurShare->id) {
            return;
        }
        
        try {
            $auteurPublication->notify(new NewShareNotification($auteurShare, $publication, $plateforme));
            Log::info("Notification nouveau partage envoyée à {$auteurPublication->email}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi notif nouveau partage: " . $e->getMessage());
        }
    }

    /**
     * Envoie une alerte signalement publication (pour admin)
     */
    public function sendPublicationReportedAlert(Report $report): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PublicationReportedNotification($report->user, $report->publication, $report->motif));
            }
            Log::info("Alerte signalement envoyée aux admins");
        } catch (\Exception $e) {
            Log::error("Erreur envoi alerte signalement: " . $e->getMessage());
        }
    }

    /**
     * Envoie une notification signalement résolu (à l'auteur)
     */
    public function sendReportResolvedAlert(Publication $publication, string $action): void
    {
        try {
            $auteur = $publication->user;
            $auteur->notify(new ReportResolvedNotification($publication, $action));
            Log::info("Notification signalement résolu envoyée à {$auteur->email}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi notif signalement résolu: " . $e->getMessage());
        }
    }

    /**
     * Envoie une alerte à tous les admins
     */
    public function sendAdminAlert(string $title, string $message, string $type = 'info'): void
    {
        try {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AdminAlertNotification($title, $message, $type));
            }
            Log::info("Alerte admin envoyée: {$title}");
        } catch (\Exception $e) {
            Log::error("Erreur envoi alerte admin: " . $e->getMessage());
        }
    }
}