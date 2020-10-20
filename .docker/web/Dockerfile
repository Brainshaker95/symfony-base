ARG DOCKER_PHP_VERSION=7.4
FROM php:${DOCKER_PHP_VERSION}-fpm

###########################################################################
# userid of `app` user (linux support file permissions)
###########################################################################
ARG DOCKER_APP_USER_ID=1000
ARG DOCKER_APP_GROUP_ID=1000
ENV DOCKER_APP_USER_ID ${DOCKER_APP_USER_ID}
ENV DOCKER_APP_GROUP_ID ${DOCKER_APP_GROUP_ID}
RUN groupadd --gid ${DOCKER_APP_GROUP_ID} app \
    && useradd --uid ${DOCKER_APP_USER_ID} -g app --create-home app

###########################################################################
# nginx:
###########################################################################
RUN apt-get update \
    && apt-get install -y nginx  \
    && rm -rf /var/lib/apt/lists/*
RUN rm /etc/nginx/sites-enabled/default

###########################################################################
# PHP:
###########################################################################

RUN mv /usr/local/etc/php-fpm.d/www.conf /usr/local/etc/php-fpm.d/www.conf.backup
COPY ./www.conf /usr/local/etc/php-fpm.d/www.conf

RUN apt-get update && apt-get install zlib1g-dev -y \
        libicu-dev \
        libmcrypt-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        supervisor \
        git \
        libzip-dev \
        zip \
        libonig-dev \
        libxslt-dev \
        libcurl3-dev \
        xvfb \
        libfontconfig \
    && docker-php-ext-configure intl --enable-intl \
    && docker-php-ext-configure pcntl --enable-pcntl \
    && docker-php-ext-configure mysqli --with-mysqli \
    && docker-php-ext-configure curl --with-curl \
    && docker-php-ext-configure mbstring --enable-mbstring \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        curl \
        pdo \
        pdo_mysql \
        mysqli \
        gd \
        zip \
        intl \
        iconv \
        pcntl \
        xsl

###########################################################################
# install Imagemagick
###########################################################################
ARG DOCKER_INSTALL_IMAGEMAGICK=false
ENV DOCKER_INSTALL_IMAGEMAGICK ${DOCKER_INSTALL_IMAGEMAGICK}
RUN if [ ${DOCKER_INSTALL_IMAGEMAGICK} = true ]; then \
    apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/* \
    printf "\n" | pecl install imagick \
    && docker-php-ext-enable imagick \
;fi

###########################################################################
## redis:
###########################################################################
RUN if [ ${DOCKER_INSTALL_REDIS} = true ]; then \
    pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis \
;fi


###########################################################################
# composer:
###########################################################################
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
USER root
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN mkdir -p /home/app/.composer && chown -R app /home/app/.composer && chmod -R u+w /home/app/.composer
USER app
ENV PATH="${PATH}:/home/app/.composer/vendor/bin"

###########################################################################
# nodejs / npm and yarn via nvm:
###########################################################################
USER root

ARG DOCKER_NODE_VERSION=12.18.4
ENV NODE_VERSION=${DOCKER_NODE_VERSION}
ARG DOCKER_YARN_VERSION=1.22.4
ENV YARN_VERSION=${DOCKER_YARN_VERSION}
ENV NVM_DIR=/usr/local/nvm
RUN mkdir -p $NVM_DIR \
    && curl https://raw.githubusercontent.com/nvm-sh/nvm/v0.36.0/install.sh | bash \
    && . $NVM_DIR/nvm.sh \
    && nvm install $NODE_VERSION \
    && nvm alias default $NODE_VERSION \
    && nvm use default \
    && npm install yarn@$YARN_VERSION -g \
    && chown -R app: $NVM_DIR \
    && chmod a+x $NVM_DIR/nvm.sh \
    && mkdir -p /home/app/.cache \
    && mkdir -p /home/app/.npm \
    && mkdir -p /home/app/.yarn \
    && chown -R app: /home/app/.cache \
    && chown -R app: /home/app/.npm \
    && chown -R app: /home/app/.yarn

# Setup bashrc
RUN echo | tee -a /etc/skel/.bashrc >> /etc/bash.bashrc \
    && echo ". \$NVM_DIR/nvm.sh" | tee -a /etc/skel/.bashrc >> /etc/bash.bashrc \
    && echo | tee -a /etc/skel/.bashrc >> /etc/bash.bashrc
RUN touch /home/app/.yarnrc \
    && chown app: /home/app/.yarnrc

###########################################################################
# ssh:
###########################################################################
USER root

RUN apt-get update && apt-get install -y openssh-client

ADD /ssh/config-root /root/.ssh/config
ADD /ssh/config-app /home/app/.ssh/config

RUN chown -R root:root /root/.ssh \
    && chown -R app:app /home/app/.ssh

###########################################################################
# phpunit via ssh:
###########################################################################
USER root
# add support to connect via ssh for unit tests
COPY ./ssh/insecure_id_rsa.pub /tmp/id_rsa.pub

RUN apt-get update && apt-get install -y openssh-server \
  && rm -f /etc/service/sshd/down \
  && cat /tmp/id_rsa.pub >> /home/app/.ssh/authorized_keys \
  && rm -f /tmp/id_rsa* \
  && chmod 644 /home/app/.ssh/authorized_keys


###########################################################################
# https support:
###########################################################################
USER root

# copy ssl key
COPY ssl-keys/localhost.crt /etc/ssl/localhost/localhost.crt
COPY ssl-keys/localhost.key /etc/ssl/localhost/localhost.key

###########################################################################
# supervisor:
###########################################################################
# supervisor to execute queues or crontab
RUN apt-get update && apt-get install supervisor -y
COPY supervisord.conf /etc/supervisord.conf

###########################################################################
# crontab support:
###########################################################################
RUN apt-get update && apt-get install cron -y
COPY crontab /etc/crontab
RUN chmod 644 /etc/crontab
RUN crontab /etc/crontab

STOPSIGNAL SIGTERM

COPY entrypoint /var/run/entrypoint
RUN chmod +x /var/run/entrypoint

ENTRYPOINT ["/var/run/entrypoint"]
