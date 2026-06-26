#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# Configuration
echo "📋 Configuration:"
echo "  APP_ENV: $APP_ENV"
echo "  DB_HOST: $DB_HOST"
echo "  DB_PORT: $DB_PORT"
echo "  DB_DATABASE: $DB_DATABASE"
echo "  DB_USERNAME: $DB_USERNAME"

# Fonction pour vérifier la base de données
check_database() {
    php artisan db:show > /dev/null 2>&1
    return $?
}

# Attendre la base de données
echo "⏳ Attente de la base de données..."
for i in {1..60}; do
    if check_database; then
        echo "✅ Base de données accessible!"
        break
    fi
    echo "   Tentative $i/60..."
    sleep 2
done

# Si la base de données n'est pas accessible, continuer quand même
if ! check_database; then
    echo "⚠️ La base de données n'est pas accessible, mais on continue..."
fi

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
nohup php artisan schedule:work > /var/log/scheduler.log 2>&1 &

# Démarrer Apache
echo "🌐 Démarrage du serveur Apache..."
apache2-foreground