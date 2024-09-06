FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

#Generate Self-Signed Certificate

COPY . /var/www/html/
COPY ./000-default.conf /etc/apache2/sites-enabled/000-default.conf
RUN echo 'ServerName 127.0.0.1' >> /etc/apache2/apache2.conf

RUN --mount=type=cache,target=/var/cache/apt \
    echo 'CONFIG START' &&\
    chown www-data:www-data /var/www/html &&\
    echo 'CONFIG FINISH'

EXPOSE 80
CMD /usr/sbin/apache2ctl -D FOREGROUND