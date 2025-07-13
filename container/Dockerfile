FROM registry.access.redhat.com/ubi10/ubi

LABEL maintainer="Adarsha HD <me@adarshahd.com>"
LABEL org.opencontainers.image.title="News Aggregator"
LABEL org.opencontainers.image.description="A news aggregator application using Laravel"
LABEL org.opencontainers.image.source="https://github.com/adarshahd/aggregator"
LABEL org.opencontainers.image.licenses="AGPL"

RUN dnf update --refresh -y && \
    dnf -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-10.noarch.rpm && \
    dnf -y install https://rpms.remirepo.net/enterprise/remi-release-10.rpm && \
    dnf -y module switch-to php:remi-8.4 && \
    dnf -y install composer php php-pdo php-gd php-zip php-yaml php-redis php-mysqlnd php-fpm unzip && \
    dnf -y install supervisor && \
    dnf clean all && \
    mkdir /run/php-fpm

COPY . /var/www/html/

COPY container/supervisord/*.ini /etc/supervisord.d/
COPY container/startup.sh /usr/local/bin/

WORKDIR /var/www/html

RUN composer install --prefer-dist && \
    php artisan octane:install --server=frankenphp

RUN php artisan scribe:generate

RUN systemctl enable supervisord && \
    chmod +x /usr/local/bin/startup.sh && \
    rm -rf container

CMD [ "startup.sh" ]
