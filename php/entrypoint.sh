#!/bin/sh
# php/entrypoint.sh

# Проверяем, существует ли шаблон конфига
if [ -f /var/www/html/common/config/main-local.template.php ]; then
  # Используем envsubst для подстановки переменных окружения
  # и создаем финальный файл конфигурации
  echo "Generating main-local.php from template..."
  envsubst < /var/www/html/common/config/main-local.template.php > /var/www/html/common/config/main-local.php
  echo "Done."
fi


echo "Waiting for database connection..."
until php -r "
try {
  \$pdo = new PDO('mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}', '${DB_USER}', '${DB_PASSWORD}');
  echo 'Database connected';
  exit(0);
} catch (Exception \$e) {
  exit(1);
}
"; do
  echo "Database not ready, waiting..."
  sleep 2
done
echo "Database is ready."

# Применение миграций (запускаем из директории приложения)
cd /var/www/html
if [ ! -f common/config/main-local.php ]; then
  echo "Config not generated, skipping migrations."
  exit 1
fi
./yii migrate/up --interactive=0
echo "Migrations applied."

# Выполняем команду, которая была передана в CMD (в нашем случае - php-fpm)
exec "$@"