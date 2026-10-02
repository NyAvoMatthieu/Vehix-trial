#!/bin/bash

# ==============================
# 1. Lancer MariaDB
# ==============================
gnome-terminal -- bash -c '
cd /opt/mariadb-10.11 || exit 1

read -p "Lancer MariaDB ? [o/N] - [y/N] : " choix

if [[ "$choix" =~ ^[oOyY]$ ]]; then
    echo "Démarrage de MariaDB..."
    
    ./bin/mariadbd \
        --datadir="$HOME/mariadb-data" \
        --socket=/tmp/mariadb.sock \
        --port=3306
else
    echo "MariaDB n'\''a pas été lancé."
    exec bash
fi
'

# ==============================
# 2. Attendre que MariaDB soit prêt
# ==============================
echo "Vérification de MariaDB..."

for i in {1..15}; do
    if mysqladmin --socket=/tmp/mariadb.sock ping --silent 2>/dev/null; then
        echo "MariaDB est actif."
        break
    fi

    sleep 1
done

# ==============================
# 3. Lancer Laravel
# ==============================
if mysqladmin --socket=/tmp/mariadb.sock ping --silent 2>/dev/null; then

    gnome-terminal -- bash -c '
    cd "$(pwd)" || exit 1
    php artisan serve
    exec bash
    '

    # Lancer les queues (pour les notifications)
    gnome-terminal -- bash -c '
    cd "$(pwd)" || exit 1
    php artisan queue:work
    exec bash
    '

    # ==============================
    # 4. Lancer Vite
    # ==============================
    gnome-terminal -- bash -c '
    cd "$(pwd)" || exit 1
    npm run dev
    exec bash
    '

else
    echo "MariaDB n'\''est pas actif."
    echo "Laravel et Vite ne seront pas lancés."
fi