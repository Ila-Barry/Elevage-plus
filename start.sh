#!/bin/bash
# start.sh

echo "🚀 Démarrage de l'application Élevage+ sur Render"
echo "=================================================="

# 1. Créer tous les dossiers nécessaires
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

# 2. 🔗 FORCER la création du lien symbolique (méthode fiable)
echo "🔗 Création du lien symbolique storage..."
# Supprimer l'ancien lien s'il existe (fichier ou dossier)
rm -rf /var/www/html/public/storage
# Créer le lien symbolique
ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

# 3. ✅ Vérifier que le lien est créé
if [ -L "/var/www/html/public/storage" ]; then
    echo "✅ Lien symbolique storage créé avec succès !"
else
    echo "❌ Échec de la création du lien symbolique"
    # Tentative de secours avec php artisan
    php artisan storage:link
fi

# 4. Nettoyer les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 5. Configuration
echo "📋 Configuration :"
echo "  APP_ENV: $APP_ENV"
echo "  APP_URL: $APP_URL"

# 6. ⭐ AJOUT : Créer un fichier de test pour vérifier le lien
echo "🔍 Création d'un fichier de test..."
echo "Le dossier storage est accessible !" > /var/www/html/storage/app/public/test.txt
# Vérifier si le fichier est accessible via le lien
if [ -f "/var/www/html/public/storage/test.txt" ]; then
    echo "✅ Le lien fonctionne correctement !"
else
    echo "⚠️ Le lien semble ne pas fonctionner correctement"
fi

# 7. Permissions
echo "🔐 Application des permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

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