#!/bin/sh
# Démarre le conteneur Laravel du portfolio : attend MySQL, applique les migrations,
# met la configuration en cache, puis lance php-fpm.
set -e

cd /var/www/html

if [ -z "$APP_KEY" ]; then
    echo "ERREUR : APP_KEY est vide dans le fichier .env."
    echo "Générez-la avec : echo \"APP_KEY=base64:\$(openssl rand -base64 32)\" >> .env"
    exit 1
fi

# Sur un nouveau serveur, le volume « storage » est vide : on recrée ses dossiers.
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Attente de MySQL (environ 2 minutes au maximum).
tries=0
until php -r "new PDO('mysql:host='.getenv('DB_HOST').';port='.(getenv('DB_PORT') ?: 3306), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" >/dev/null 2>&1; do
    tries=$((tries + 1))
    if [ "$tries" -ge 60 ]; then
        echo "ERREUR : MySQL ne répond pas (hôte ${DB_HOST}). Vérifiez DB_HOST, DB_USERNAME et DB_PASSWORD."
        exit 1
    fi
    echo "En attente de MySQL… ($tries)"
    sleep 2
done

rm -f /tmp/portfolio-ready
su-exec www-data php artisan migrate --force --no-interaction

# Configuration, routes, vues et événements en cache (le .env est lu à ce moment-là).
su-exec www-data php artisan optimize --no-interaction >/dev/null

touch /tmp/portfolio-ready
echo "Portfolio prêt."

exec "$@"
