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
echo "  SSL_CA: $MYSQL_ATTR_SSL_CA"

# Attendre que la base de données soit prête
echo "⏳ Attente de la base de données..."
for i in {1..30}; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Base de données accessible!"
        break
    fi
    echo "   Tentative $i/30..."
    sleep 2
done

# Configurer la base de données
echo "📦 Configuration de la base de données..."
php artisan db:setup

# Vider le cache
echo "🧹 Vidage du cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
echo "⚡ Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

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