FROM --platform=linux/amd64 ubuntu:22.04

ARG NODE_VERSION=22

WORKDIR /var/www/html

# Disable frontend dialogs
ENV DEBIAN_FRONTEND=noninteractive
ENV PHP_VERSION=8.4
ENV ACCEPT_EULA=Y
ENV LANG=en_US.UTF-8
ENV LC_ALL=en_US.UTF-8

# Add ppa, curl and syslogd
RUN apt-get update && apt-get install -y software-properties-common curl inetutils-syslogd \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update && apt-get install -y \
    nodejs \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-gettext \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-ast \
    php${PHP_VERSION}-memcached \
    php${PHP_VERSION}-gd \
    zip \
    unzip \
    git \
    cron \
    nodejs \
    socat \
    libgmp-dev \
    libgmp10 \
    nginx && \
    apt-get autoremove -y && apt-get clean && rm -rf /var/lib/apt/lists/* && \
    mkdir -p /run/php && chmod -R 755 /run/php && \
    sed -i 's|.*listen =.*|listen=9000|g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i 's|.*error_log =.*|error_log=/proc/self/fd/2|g' /etc/php/${PHP_VERSION}/fpm/php-fpm.conf && \
    sed -i 's|.*access.log =.*|access.log=/proc/self/fd/2|g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i 's|.*user =.*|user=root|g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i 's|.*group =.*|group=root|g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i 's#.*variables_order.*#variables_order=EGPCS#g' /etc/php/${PHP_VERSION}/fpm/php.ini && \
    sed -i 's#.*memory_limit.*#memory_limit=512M#g' /etc/php/${PHP_VERSION}/fpm/php.ini && \
    sed -i 's#.*date.timezone.*#date.timezone=UTC#g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf && \
    sed -i 's#.*clear_env.*#clear_env=no#g' /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# Basis-Pakete, Locale, ODBC
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      ca-certificates curl gnupg2 locales apt-transport-https \
      software-properties-common \
      build-essential pkg-config \
      php${PHP_VERSION}-dev \
      php-pear \
      unixodbc unixodbc-dev libgssapi-krb5-2 \
 && echo "en_US.UTF-8 UTF-8" > /etc/locale.gen \
 && locale-gen

RUN npm config set cache /tmp/npm --global

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy NGINX config
COPY ./docker/nginx.conf /etc/nginx/nginx.conf

COPY ./docker/start.sh /start.sh
COPY . /var/www/html
COPY .env.prod /var/www/html/.env

RUN chmod 755 /start.sh

EXPOSE 80
ENV PHP_BINARY=/usr/bin/php
CMD ["sh", "/start.sh"]
