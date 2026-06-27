#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# 1. Nettoyer les caches de build Docker pour forcer la lecture des variables Render
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 2. Configuration injectée (Vérification dans les logs)
echo "📋 Configuration lue depuis Render :"
echo "  APP_ENV: $APP_ENV"
echo "  DB_HOST: $DB_HOST"
echo "  DB_PORT: $DB_PORT"
echo "  DB_DATABASE: $DB_DATABASE"
echo "  DB_USERNAME: $DB_USERNAME"
echo "  MYSQL_ATTR_SSL_CA: $MYSQL_ATTR_SSL_CA"

# 3. Fonction native PHP pour tester la connexion avec le bon chemin SSL
check_database() {
    php -r "
    try {
        \$db = new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA') ?: '/var/www/html/aiven-ca.pem'
        ]);
        exit(0);
    } catch (Exception \$e) {
        fwrite(STDERR, \$e->getMessage() . \"\n\");
        exit(1);
    }
    "
}

# 4. Attendre la base de données Aiven
echo "⏳ Attente de la base de données Aiven..."
for i in {1..20}; do
    if check_database; then
        echo "✅ Base de données accessible !"
        break
    fi
    echo "   Tentative $i/20... (Vérification SSL/Réseau)"
    sleep 3
done

# 5. Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 6. Recréer l'optimisation proprement pour la production
echo "⚡ Optimisation finale de Laravel..."
php artisan config:cache
php artisan route:cache

if [ -d "/var/www/html/resources/views" ]; then
    php artisan view:cache
fi

# 7. Lancer les services d'arrière-plan
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet &

echo "⏰ Démarrage du scheduler..."
nohup php artisan schedule:work > /var/log/scheduler.log 2>&1 &

# 8. Démarrer Apache au premier plan
echo "🌐 Démarrage du serveur Apache..."
exec apache2-foreground