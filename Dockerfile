# Используем базовый образ PHP
FROM php:8.1-fpm

# Установка необходимых зависимостей для PHP и Composer
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Задание рабочей директории
WORKDIR /app
