<?php
// app/Http/Controllers/Api/Admin/AdminNewsletterController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\SendNewsletterRequest;
use App\Models\User;
use App\Services\AlertService;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\NewsletterMail;

class AdminNewsletterController extends Controller
{
    use ApiResponseTrait;

    protected AlertService $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
        $this->middleware(['auth:api', 'admin']);
    }

    public function send(SendNewsletterRequest $request)
    {
        try {
            $subject = $request->sujet;
            $content = $request->contenu;
            $targets = $request->cibles;

            $query = User::where('email_notifications', true);

            switch ($targets) {
                case 'eleveurs_uniquement':
                    $query->where('role', 'eleveur');
                    break;
                case 'admins_uniquement':
                    $query->where('role', 'admin');
                    break;
                case 'tous':
                default:
                    break;
            }

            $recipients = $query->get();

            if ($recipients->isEmpty()) {
                return $this->errorResponse('Aucun destinataire trouvé.', 422);
            }

            // Envoyer les emails
            foreach ($recipients as $user) {
                Mail::to($user->email)->queue(new NewsletterMail($subject, $content, $user));
            }

            // Utiliser AlertService avec la bonne méthode
            $this->alertService->sendAdminAlert(
                '📧 Newsletter envoyée',
                "Une newsletter a été envoyée à {$recipients->count()} utilisateur(s). Sujet: {$subject}",
                'success'
            );

            return $this->successResponse([
                'total_envoyes' => $recipients->count(),
                'cibles' => $targets,
            ], "Newsletter envoyée à {$recipients->count()} utilisateur(s).");

        } catch (\Exception $e) {
            Log::error('Erreur envoi newsletter: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de l\'envoi de la newsletter.', 500);
        }
    }
}