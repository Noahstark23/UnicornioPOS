FROM php:8.2-apache

# 1. Instalar dependencias del sistema y extensiones PHP requeridas
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    zip

# 2. Habilitar mod_rewrite de Apache (Crítico para URLs amigables)
RUN a2enmod rewrite

# 3. Copiar el código fuente al contenedor
COPY . /var/www/html/

# 4. Configurar permisos para carpetas de escritura (fotos, sesiones, backups)
# El usuario de Apache (www-data) necesita escribir aquí
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/fotos \
    && chmod -R 755 /var/www/html/backups \
    && mkdir -p /var/www/html/sessions \
    && chmod -R 755 /var/www/html/sessions

# 5. Configurar PHP para producción (Opcional: aumentar límites)
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# 6. Exponer puerto 80
EXPOSE 80
