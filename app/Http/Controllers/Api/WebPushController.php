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

    public function unsubscribe(Request $request)
    {
        try {
            $user = $request->user();
            $endpoint = $request->input('endpoint');
            
            WebPushSubscription::where('user_id', $user->id)
                ->where('endpoint', $endpoint)
                ->delete();
            
            Log::info('✅ Désabonnement push réussi', [
                'user_id' => $user->id
            ]);
            
            return $this->successResponse(null, 'Désabonnement réussi.');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur désabonnement push', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors du désabonnement.', 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $user = $request->user();
            $oldEndpoint = $request->input('old_endpoint');
            $newSubscription = $request->input('subscription');
            
            WebPushSubscription::where('user_id', $user->id)
                ->where('endpoint', $oldEndpoint)
                ->update([
                    'endpoint' => $newSubscription['endpoint'],
                    'public_key' => $newSubscription['keys']['p256dh'],
                    'auth_token' => $newSubscription['keys']['auth'],
                ]);
            
            Log::info('✅ Mise à jour abonnement push', [
                'user_id' => $user->id
            ]);
            
            return $this->successResponse(null, 'Abonnement mis à jour.');
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur mise à jour abonnement push', [
                'error' => $e->getMessage()
            ]);
            
            return $this->errorResponse('Erreur lors de la mise à jour.', 500);
        }
    }
}