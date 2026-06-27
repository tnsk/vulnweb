# ./app/Dockerfile — KASITLI ZAYIF EĞİTİM İMAJI. SADECE LOKAL KULLAN.
FROM php:8.4-apache

# --- GD (image-upload / EXIF labları) için sistem kütüphaneleri ---
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libzip-dev \
        unzip \
        iputils-ping \
    && rm -rf /var/lib/apt/lists/*

# --- PHP eklentileri: DB sürücüleri (SQLi labları) + GD (upload labları) ---
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" mysqli pdo_mysql gd

# --- Apache: .htaccess tabanlı lablar için mod_rewrite ---
RUN a2enmod rewrite

# --- KASITLI: .phtml/.pht/.php5 de PHP olarak çalıştırılsın (upload bypass labları) ---
RUN printf '%s\n' \
    '<FilesMatch "\.(php|phtml|pht|php5|phar)$">' \
    '    SetHandler application/x-httpd-php' \
    '</FilesMatch>' \
    > /etc/apache2/conf-enabled/zz-php-exec.conf

# --- KASITLI ZAYIF php.ini (development ini + override) ---
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY php-insecure.ini "$PHP_INI_DIR/conf.d/zz-insecure-lab.ini"

# Uygulama kaynağı (compose'da bind-mount da edilir, canlı düzenleme için)
COPY ./src/ /var/www/html/

# Upload dizini yazılabilir olsun
RUN mkdir -p /var/www/html/hackable/uploads /var/www/html/logs \
    && chown -R www-data:www-data /var/www/html/hackable /var/www/html/logs

LABEL org.opencontainers.image.description="DELIBERATELY INSECURE TRAINING APP — DO NOT EXPOSE"
EXPOSE 80
