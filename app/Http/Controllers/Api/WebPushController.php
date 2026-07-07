<?php
// app/Http/Controllers/Api/WebPushController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\WebPushSubscription;

class WebPushController extends Controller
{
    use ApiResponseTrait;

    /**
     * S'abonner aux notifications push
     */
    public function subscribe(Request $request)
    {
        try {
            $user = $request->user();
            
            $subscription = WebPushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $request->input('subscription.endpoint'),
                ],
                [
                    'public_key' => $request->input('subscription.keys.p256dh'),
                    'auth_token' => $request->input('subscription.keys.auth'),
                    'content_encoding' => 'aesgcm',
                ]
            );
            
            Log::info('✅ Abonnement push enregistré', [
                'user_id' => $user->id,
                'endpoint' => $subscription->endpoint
            ]);
            
            return $this->successResponse($subscription, 'Abonnement aux notifications push réussi.');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur abonnement push', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Erreur lors de l\'abonnement aux notifications.', 500);
        }
    }

    /**
     * Se désabonner des notifications push
     */
    public function unsubscribe(Request $request)
    {
        try {
            $user = $request->user();
            $endpoint = $request->input('endpoint');
            
            if (!$endpoint) {
                return $this->errorResponse('Endpoint non fourni.', 422);
            }
            
            $deleted = WebPushSubscription::where('user_id', $user->id)
                ->where('endpoint', $endpoint)
                ->delete();
            
            Log::info('✅ Désabonnement push réussi', [
                'user_id' => $user->id,
                'deleted' => $deleted
            ]);
            
            return $this->successResponse(null, 'Désabonnement réussi.');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur désabonnement push', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors du désabonnement.', 500);
        }
    }

    /**
     * Mettre à jour l'abonnement push
     */
    public function update(Request $request)
    {
        try {
            $user = $request->user();
            $oldEndpoint = $request->input('old_endpoint');
            $newSubscription = $request->input('subscription');
            
            // ✅ Vérifier que les données sont présentes
            if (!$newSubscription || !isset($newSubscription['endpoint'])) {
                return $this->errorResponse('Données d\'abonnement incomplètes.', 422);
            }
            
            // ✅ Si un ancien endpoint est fourni, le supprimer
            if ($oldEndpoint) {
                WebPushSubscription::where('user_id', $user->id)
                    ->where('endpoint', $oldEndpoint)
                    ->delete();
                
                Log::info('🗑️ Ancien abonnement supprimé', [
                    'user_id' => $user->id,
                    'old_endpoint' => $oldEndpoint
                ]);
            }
            
            // ✅ Créer ou mettre à jour le nouvel abonnement
            $subscription = WebPushSubscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'endpoint' => $newSubscription['endpoint'],
                ],
                [
                    'public_key' => $newSubscription['keys']['p256dh'] ?? '',
                    'auth_token' => $newSubscription['keys']['auth'] ?? '',
                    'content_encoding' => 'aesgcm',
                ]
            );
            
            Log::info('✅ Mise à jour abonnement push réussie', [
                'user_id' => $user->id,
                'new_endpoint' => $subscription->endpoint
            ]);
            
            return $this->successResponse($subscription, 'Abonnement mis à jour avec succès.');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour abonnement push', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('Erreur lors de la mise à jour de l\'abonnement.', 500);
        }
    }

    /**
     * Récupérer l'état de l'abonnement push
     */
    public function status(Request $request)
    {
        try {
            $user = $request->user();
            
            $subscription = WebPushSubscription::where('user_id', $user->id)->first();
            
            return $this->successResponse([
                'subscribed' => $subscription !== null,
                'subscription' => $subscription,
                'vapid_public_key' => config('webpush.vapid.public_key'),
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur récupération statut push', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors de la récupération du statut.', 500);
        }
    }
}