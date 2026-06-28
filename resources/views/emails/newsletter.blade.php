{{-- resources/views/emails/newsletter.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4F46E5;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #4F46E5;
        }
        .content {
            color: #1f2937;
            line-height: 1.7;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background: #4F46E5;
            color: #ffffff;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
        .button:hover {
            background: #4338CA;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🐄 Élevage+</div>
        </div>

        <div class="content">
            <p><strong>Bonjour {{ $user->name }} !</strong></p>

            {!! nl2br(e($content)) !!}

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/dashboard') }}" class="button">Accéder à mon espace</a>
            </div>
        </div>

        <div class="footer">
            <p>
                Vous recevez cet email car vous êtes inscrit sur <strong>Élevage+</strong>.<br>
                Pour ne plus recevoir ces emails, modifiez vos préférences dans votre espace utilisateur.
            </p>
            <p>&copy; {{ date('Y') }} Élevage+. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>