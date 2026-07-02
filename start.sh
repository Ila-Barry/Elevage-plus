#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# 1. Créer tous les dossiers requis
echo "📂 Vérification et création des dossiers de stockage..."
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/app/public/elevages
mkdir -p /var/www/html/storage/app/public/animaux
mkdir -p /var/www/html/storage/app/public/avatars
mkdir -p /var/www/html/storage/app/public/uploads/publications/images

# 2. 🔗 SUPPRIMER ET RECRÉER LE LIEN SYMBOLIQUE STORAGE
echo "🔗 Suppression et recréation du lien symbolique storage..."
rm -rf /var/www/html/public/storage
php artisan storage:link

# 3. Vérifier le lien symbolique
if [ -L "/var/www/html/public/storage" ]; then
    echo "✅ Lien symbolique storage présent"
    ls -la /var/www/html/public/storage
else
    echo "⚠️ Lien symbolique storage manquant, création forcée..."
    ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
fi

# 4. ✅ VÉRIFICATION CRITIQUE : Tester l'accès aux fichiers
echo "🔍 Test d'accès aux fichiers..."
if [ -f "/var/www/html/storage/app/public/elevages/elevage_1782899798_71avruX3w0.png" ]; then
    echo "✅ Fichier elevage trouvé dans storage/app/public/elevages/"
else
    echo "⚠️ Fichier elevage non trouvé dans storage/app/public/elevages/"
    echo "   Recherche dans d'autres emplacements..."
    find /var/www/html/storage -name "elevage_1782899798_71avruX3w0.png" 2>/dev/null
fi

# 5. Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 6. Configuration injectée
echo "📋 Configuration lue depuis Render :"
echo "  APP_ENV: $APP_ENV"
echo "  APP_URL: $APP_URL"
echo "  DB_HOST: $DB_HOST"
echo "  DB_PORT: $DB_PORT"
echo "  DB_DATABASE: $DB_DATABASE"
echo "  DB_USERNAME: $DB_USERNAME"

# 7. Fonction de test de la base de données
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

# 8. Attendre la base de données
echo "⏳ Attente de la base de données Aiven..."
for i in {1..20}; do
    if check_database; then
        echo "✅ Base de données accessible !"
        break
    fi
    echo "   Tentative $i/20..."
    sleep 3
done

# 9. Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 10. Optimisation
echo "⚡ Optimisation finale de Laravel..."
php artisan config:cache
php artisan route:cache

# 11. 🔐 PERMISSIONS
echo "🔐 Application des permissions www-data sur storage..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Vérification finale du lien
echo "🔗 Vérification finale du lien symbolique :"
ls -la /var/www/html/public/ | grep storage

# 13. Lancer les services
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet &

echo "⏰ Démarrage du scheduler..."
nohup php artisan schedule:work > /var/log/scheduler.log 2>&1 &

# 14. Démarrer Apache
echo "🌐 Démarrage du serveur Apache..."
exec apache2-foreground