#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# Afficher les variables d'environnement (sans les mots de passe)
echo "📋 Configuration:"
echo "  APP_ENV: $APP_ENV"
echo "  DB_HOST: $DB_HOST"
echo "  DB_PORT: $DB_PORT"
echo "  DB_DATABASE: $DB_DATABASE"
echo "  DB_USERNAME: $DB_USERNAME"

# Attendre que la base de données soit prête (max 60 secondes)
echo "⏳ Attente de la base de données..."
for i in {1..60}; do
    if php artisan migrate:status > /dev/null 2>&1; then
        echo "✅ Base de données accessible!"
        break
    fi
    echo "   Tentative $i/60..."
    sleep 2
done

# Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# Optimiser Laravel
echo "⚡ Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Lancer le worker de queue en arrière-plan
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet &

# Lancer le scheduler
echo "⏰ Démarrage du scheduler..."
while true; do
    php artisan schedule:run >> /var/log/scheduler.log 2>&1
    sleep 60
done &

# Démarrer Apache
echo "🌐 Démarrage du serveur Apache..."
apache2-foreground