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

# 2. Créer des images par défaut si elles n'existent pas
echo "🖼️ Création des images par défaut..."
if [ ! -f "/var/www/html/public/images/img-elevage.jpeg" ]; then
    echo "⚠️ Image par défaut manquante, création d'une image placeholder..."
    # Créer une image placeholder simple
    echo "iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAAdgAAAHYBTnsmCAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAADkSURBVDiNpZO9TsMwFIVPjR+gYkCILYIhG5WKDYkFoW7sLLxAF8QGi7RUaiUYkSgDT9CJpeMvaQCDhLlKklb4C3F0Yg/3I33nyrmxwBp/uN/e/f7eO9dmwT/BggXLqNVqSznnK5xzbC4EAULI1ZxzM+d8BQBnzhXG3DnnpZRyRinlOUtJ5xxgAfjj4/s75pwHhJBFhLmQlFIueI5rlpaHME4pObXWb5QSA7XWb87Z83+8B1wAmXNeVErZARyYmBGCUEC4Xh9FFIAPAIW9iOAchJAzpZTT0SKEYF/j2+yutc62tTb/AcEHdP0/irzN/Xua9bL/BDwbD73+DA5bP1c+AAAAAElFTkSuQmCC" | base64 -d > /var/www/html/public/images/default-image.png
fi

# 3. 🔗 FORCER la création du lien symbolique
echo "🔗 Création du lien symbolique storage..."
rm -rf /var/www/html/public/storage
ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

# 4. ✅ Vérifier que le lien est créé
if [ -L "/var/www/html/public/storage" ]; then
    echo "✅ Lien symbolique storage créé avec succès !"
    # Lister le contenu pour vérifier
    ls -la /var/www/html/public/storage/
else
    echo "❌ Échec de la création du lien symbolique"
    php artisan storage:link
fi

# 5. ✅ VÉRIFICATION CRUCIALE : Vérifier que les dossiers des publications existent
echo "🔍 Vérification des dossiers de publications..."
if [ -d "/var/www/html/storage/app/public/uploads/publications/images" ]; then
    echo "✅ Dossier des images de publications existe"
    # Compter les images
    IMAGE_COUNT=$(ls -1 /var/www/html/storage/app/public/uploads/publications/images/ 2>/dev/null | wc -l)
    echo "   📸 Images trouvées: $IMAGE_COUNT"
else
    echo "❌ Dossier des images de publications manquant !"
    mkdir -p /var/www/html/storage/app/public/uploads/publications/images
    chmod -R 775 /var/www/html/storage/app/public/uploads
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

# 8. Permissions
echo "🔐 Application des permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/public/storage

# 9. Attendre la base de données
echo "⏳ Attente de la base de données Aiven..."
for i in {1..20}; do
    if php artisan db:show > /dev/null 2>&1; then
        echo "✅ Base de données accessible !"
        break
    fi
    echo "   Tentative $i/20... (La DB ne répond pas encore)"
    sleep 3
done

# 10. Exécuter les migrations
echo "📦 Exécution des migrations..."
php artisan migrate --force

# 11. Optimisation
echo "⚡ Optimisation finale de Laravel..."
php artisan config:cache
php artisan route:cache

# 12. Vérification finale du lien
echo "🔗 Vérification finale du lien symbolique :"
ls -la /var/www/html/public/ | grep storage

# 13. Lancer les services
echo "🔄 Démarrage du worker de queue..."
php artisan queue:work --daemon --quiet > /dev/null 2>&1 &

echo "⏰ Démarrage du scheduler..."
nohup php artisan schedule:work > /tmp/scheduler.log 2>&1 &

# 14. Démarrer Apache
echo "🌐 Démarrage du serveur Apache..."
exec apache2-foreground