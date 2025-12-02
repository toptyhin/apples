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

# Выполняем команду, которая была передана в CMD (в нашем случае - php-fpm)
exec "$@"