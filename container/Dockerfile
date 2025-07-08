FROM registry.access.redhat.com/ubi10/ubi

LABEL maintainer="Adarsha HD <me@adarshahd.com>"
LABEL org.opencontainers.image.title="News Aggregator"
LABEL org.opencontainers.image.description="A news aggregator application using Laravel"
LABEL org.opencontainers.image.source="https://github.com/adarshahd/aggregator"
LABEL org.opencontainers.image.licenses="AGPL"

RUN dnf update --refresh -y && \
    dnf -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-10.noarch.rpm && \
    dnf -y install https://rpms.remirepo.net/enterprise/remi-release-10.rpm && \
    dnf -y module switch-to php:remi-8.3 && \
    dnf -y install composer php php-pdo php-gd php-zip php-fpm unzip && \
    dnf clean all && \
    mkdir /run/php-fpm

COPY . /var/www/html/

WORKDIR /var/www/html

RUN composer install --prefer-dist && \
    php artisan octane:install --server=frankenphp

RUN touch database/database.sqlite && \
    php artisan migrate --force

CMD [ "php", "/var/www/html/artisan", "octane:start", "--server=frankenphp" ]
