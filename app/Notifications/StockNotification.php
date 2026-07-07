<?php
// app/Notifications/StockNotification.php

namespace App\Notifications;

use App\Models\Produit;
use App\Models\MouvementStock;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;
use Illuminate\Support\Facades\Log;

class StockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $produit;
    protected $mouvement;
    protected string $type; // 'created', 'updated', 'deleted', 'stock_entree', 'stock_sortie', 'stock_critique', 'stock_rupture', 'stock_expiration'
    protected array $additionalData;

    /**
     * Create a new notification instance.
     */
    public function __construct($produit, string $type, $mouvement = null, array $additionalData = [])
    {
        $this->produit = $produit;
        $this->type = $type;
        $this->mouvement = $mouvement;
        $this->additionalData = $additionalData;
        
        Log::info('🔔 Notification Stock créée', [
            'produit_id' => $produit->id,
            'type' => $type,
            'user_id' => $produit->elevage?->user_id
        ]);
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['database'];
        
        if ($notifiable->web_notifications ?? true) {
            $channels[] = WebPushChannel::class;
        }
        
        if ($notifiable->email_notifications ?? false) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $data = $this->getNotificationData();
        
        Log::info('💾 Notification Stock sauvegardée en base', [
            'user_id' => $notifiable->id,
            'type' => $data['type'],
            'produit_id' => $this->produit->id
        ]);
        
        return $data;
    }

    /**
     * Get the web push representation of the notification.
     */
    public function toWebPush($notifiable, $notification)
    {
        $data = $this->getNotificationData();
        
        return (new WebPushMessage)
            ->title($data['title'])
            ->icon('/images/logo-elevage-plus.png')
            ->body($data['message'])
            ->action('Voir le stock', 'view_stock')
            ->data([
                'id' => $notification->id,
                'url' => $data['url'],
                'produit_id' => $this->produit->id
            ])
            ->badge('/images/badge.png')
            ->dir('auto')
            ->lang('fr')
            ->vibrate([200, 100, 200]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $data = $this->getNotificationData();
        $produitNom = $this->produit->nom;
        $elevageNom = $this->produit->elevage?->nom ?? 'Élevage';
        
        return (new MailMessage)
            ->subject($data['title'])
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line($data['message'])
            ->line('Détails du produit :')
            ->line('- Nom : ' . $produitNom)
            ->line('- Catégorie : ' . $this->produit->categorie_label)
            ->line('- Quantité : ' . $this->produit->quantite . ' ' . $this->produit->unite)
            ->line('- Élevage : ' . $elevageNom)
            ->action('Voir le stock', url($data['url']))
            ->line('Merci d\'utiliser la plateforme Élevage+ !')
            ->salutation('Cordialement, L\'équipe Élevage+');
    }

    /**
     * Get the notification data.
     */
    protected function getNotificationData(): array
    {
        $produitNom = $this->produit->nom;
        $quantite = $this->produit->quantite;
        $unite = $this->produit->unite_label;
        $seuil = $this->produit->seuil_alerte;
        $elevageNom = $this->produit->elevage?->nom ?? 'Élevage';
        
        switch ($this->type) {
            case 'created':
                return [
                    'title' => '📦 Nouveau produit ajouté',
                    'message' => "Le produit '{$produitNom}' a été ajouté au stock de {$elevageNom}. Quantité : {$quantite} {$unite}",
                    'type' => 'success',
                    'icon' => 'fa-box',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'updated':
                $changes = $this->additionalData['changes'] ?? [];
                $changeMessage = $this->formatChanges($changes);
                
                return [
                    'title' => '📝 Produit modifié',
                    'message' => "Le produit '{$produitNom}' a été modifié." . ($changeMessage ? " {$changeMessage}" : ''),
                    'type' => 'info',
                    'icon' => 'fa-edit',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'deleted':
                return [
                    'title' => '🗑️ Produit supprimé',
                    'message' => "Le produit '{$produitNom}' a été supprimé du stock.",
                    'type' => 'warning',
                    'icon' => 'fa-trash',
                    'url' => '/stocks',
                ];
            
            case 'stock_entree':
                $quantiteMvt = $this->mouvement?->quantite ?? 0;
                $motif = $this->mouvement?->motif_label ?? 'Non spécifié';
                $nouvelleQuantite = $this->produit->quantite;
                
                return [
                    'title' => '📥 Entrée de stock',
                    'message' => "Entrée de {$quantiteMvt} {$unite} de '{$produitNom}'. Motif : {$motif}. Nouveau stock : {$nouvelleQuantite} {$unite}",
                    'type' => 'success',
                    'icon' => 'fa-arrow-down',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'stock_sortie':
                $quantiteMvt = $this->mouvement?->quantite ?? 0;
                $motif = $this->mouvement?->motif_label ?? 'Non spécifié';
                $nouvelleQuantite = $this->produit->quantite;
                
                return [
                    'title' => '📤 Sortie de stock',
                    'message' => "Sortie de {$quantiteMvt} {$unite} de '{$produitNom}'. Motif : {$motif}. Nouveau stock : {$nouvelleQuantite} {$unite}",
                    'type' => 'warning',
                    'icon' => 'fa-arrow-up',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'stock_critique':
                return [
                    'title' => '⚠️ Stock critique',
                    'message' => "⚠️ ALERTE : Le stock de '{$produitNom}' est critique ! Quantité : {$quantite} {$unite} (Seuil : {$seuil} {$unite})",
                    'type' => 'danger',
                    'icon' => 'fa-exclamation-triangle',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'stock_rupture':
                return [
                    'title' => '🚨 Rupture de stock',
                    'message' => "🚨 URGENT : Le produit '{$produitNom}' est en rupture de stock ! Quantité : 0 {$unite}",
                    'type' => 'danger',
                    'icon' => 'fa-times-circle',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            case 'stock_expiration':
                $jours = $this->additionalData['jours'] ?? 0;
                $dateExpiration = $this->produit->date_expiration?->format('d/m/Y') ?? 'inconnue';
                
                return [
                    'title' => '📅 Produit à expiration proche',
                    'message' => "Le produit '{$produitNom}' expire dans {$jours} jours (le {$dateExpiration}). Quantité : {$quantite} {$unite}",
                    'type' => 'warning',
                    'icon' => 'fa-clock',
                    'url' => '/stocks/' . $this->produit->id,
                ];
            
            default:
                return [
                    'title' => 'Information stock',
                    'message' => "Le produit '{$produitNom}' a été modifié.",
                    'type' => 'info',
                    'icon' => 'fa-bell',
                    'url' => '/stocks/' . $this->produit->id,
                ];
        }
    }

    /**
     * Format les changements pour l'affichage
     */
    protected function formatChanges(array $changes): string
    {
        if (empty($changes)) return '';
        
        $formatted = [];
        $fieldLabels = [
            'nom' => 'Nom',
            'categorie' => 'Catégorie',
            'seuil_alerte' => 'Seuil d\'alerte',
            'unite' => 'Unité',
            'description' => 'Description',
            'fournisseur' => 'Fournisseur',
        ];
        
        foreach ($changes as $field => $values) {
            $label = $fieldLabels[$field] ?? $field;
            $old = $values['old'] ?? 'null';
            $new = $values['new'] ?? 'null';
            
            if ($field === 'categorie') {
                $old = \App\Models\Produit::CATEGORIES[$old] ?? $old;
                $new = \App\Models\Produit::CATEGORIES[$new] ?? $new;
            }
            
            if ($field === 'unite') {
                $old = \App\Models\Produit::UNITES[$old] ?? $old;
                $new = \App\Models\Produit::UNITES[$new] ?? $new;
            }
            
            $formatted[] = "{$label}: '{$old}' → '{$new}'";
        }
        
        return implode(', ', $formatted);
    }
}