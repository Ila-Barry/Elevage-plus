#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# 1. Créer TOUS les dossiers nécessaires
echo "📂 Vérification et création des dossiers de stockage..."
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/app/public/elevages
mkdir -p /var/www/html/storage/app/public/animaux
mkdir -p /var/www/html/storage/app/public/avatars
mkdir -p /var/www/html/storage/app/public/uploads
mkdir -p /var/www/html/storage/app/public/uploads/publications
mkdir -p /var/www/html/storage/app/public/uploads/publications/images
mkdir -p /var/www/html/storage/app/public/uploads/publications/videos
mkdir -p /var/www/html/storage/app/public/uploads/publications/documents

# 2. ✅ VÉRIFIER ET CRÉER LE DOSSIER AVATARS
echo "👤 Vérification du dossier avatars..."
if [ ! -d "/var/www/html/storage/app/public/avatars" ]; then
    echo "📁 Création du dossier avatars..."
    mkdir -p /var/www/html/storage/app/public/avatars
fi

# 3. Donner les permissions
echo "🔐 Application des permissions..."
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/public/storage

# 4. 🔗 FORCER la création du lien symbolique
echo "🔗 Création du lien symbolique storage..."
rm -rf /var/www/html/public/storage
ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

# 5. ✅ Vérifier que le lien est créé
if [ -L "/var/www/html/public/storage" ]; then
    echo "✅ Lien symbolique storage créé avec succès !"
else
    echo "❌ Échec de la création du lien symbolique"
    php artisan storage:link
fi

# 6. Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 7. Configuration
echo "📋 Configuration :"
echo "  APP_ENV: $APP_ENV"
echo "  APP_URL: $APP_URL"

# 8. Attendre la base de données
echo "⏳ Attente de la base de données Aiven..."
for i in {1..20}; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Base de données accessible !"
        break
    fi
    echo "   Tentative $i/20... (La DB ne répond pas encore)"
    sleep 3
done

# 9. Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 10. Optimisation
echo "⚡ Optimisation finale de Laravel..."
php artisan config:cache
php artisan route:cache

# 11. Vérification finale du lien
echo "🔗 Vérification finale du lien symbolique :"
ls -la /var/www/html/public/ | grep storage

# 12. Lancer les services
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet > /dev/null 2>&1 &

echo "⏰ Démarrage du scheduler..."
nohup php artisan schedule:work > /tmp/scheduler.log 2>&1 &

# 13. Démarrer Apache
echo "🌐 Démarrage du serveur Apache..."
exec apache2-foreground