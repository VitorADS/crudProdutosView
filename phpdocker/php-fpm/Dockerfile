FROM phpdockerio/php:8.3-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
    	php8.3-gd \
        php8.3-imagick \ 
        php8.3-memcached \ 
        php8.3-mysql \ 
        php8.3-oauth \ 
        php8.3-pgsql \ 
        php8.3-redis \ 
        php8.3-xdebug \
    && apt-get install -y nodejs \
    && apt-get install -y npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
