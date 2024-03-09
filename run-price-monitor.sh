#!/bin/bash

# Нескінченний цикл
while true; do
  # Виконання команди Laravel
  php /var/www/html/artisan price:monitor
  
  # Затримка перед наступним запуском
  sleep 300
done
