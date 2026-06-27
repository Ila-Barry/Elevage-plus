#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# 1. Utiliser le bon fichier d'environnement pour la production
if [ -f "/var/www/html/.env.render" ]; then
    echo "📝 Application du fichier .env.render..."
    cp /var/www/html/.env.render /var/www/html/.env
fi

# 2. Forcer le nettoyage des caches générés pendant le build Docker
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 3. Charger les variables d'environnement à jour pour l'affichage de contrôle
echo "📋 Configuration injectée :"
echo "  APP_ENV: $APP_ENV"
echo "  DB_HOST: mysql-35ac8206-barryila20-f192.h.aivencloud.com"
echo "  DB_PORT: 12747"
echo "  DB_DATABASE: defaultdb"
echo "  DB_USERNAME: avnadmin"

# 4. Fonction native PHP pour tester la connexion avec SSL obligatoire pour Aiven
check_database() {
    php -r "
    try {
        \$db = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA')
        ]);
        exit(0);
    } catch (Exception \$e) {
        fwrite(STDERR, \$e->getMessage() . \"\n\");
        exit(1);
    }
    "
}

# 5. Attendre la base de données
echo "⏳ Attente de la base de données Aiven..."
for i in {1..30}; do
    if check_database; then
        echo "✅ Base de données accessible !"
        break
    fi
    echo "   Tentative $i/30..."
    sleep 3
done

# 6. Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 7. Recréer l'optimisation proprement pour la production
echo "⚡ Optimisation finale de Laravel..."
php artisan config:cache
php artisan route:cache

if [ -d "/var/www/html/resources/views" ]; then
    php artisan view:cache
fi

# 8. Lancer les services d'arrière-plan
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet &

echo "⏰ Démarrage du scheduler..."
nohup php artisan schedule:work > /var/log/scheduler.log 2>&1 &

# 9. Démarrer Apache au premier plan
echo "🌐 Démarrage du serveur Apache..."
exec apache2-foreground