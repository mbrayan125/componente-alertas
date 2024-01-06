# Prepare the required software
FROM php:8.3.0-apache-bullseye
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apt-get update && \
    apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    nano

# Files to map ports on server
COPY container-files/apache-conf/ports.conf /etc/apache2/ports.conf
COPY container-files/apache-conf/user-alerts-component.conf /etc/apache2/sites-available

# Activate site port
RUN a2dissite 000-default.conf
RUN a2ensite user-alerts-component.conf
RUN a2enmod rewrite

# Perform database connection
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Enable xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY container-files/xdebug/xdebug.ini /usr/local/etc/php/conf.d
EXPOSE 9000

# Configure container
WORKDIR /app/user-alerts-component