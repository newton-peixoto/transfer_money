FROM php:7.4-fpm
RUN apt-get update && apt-get install -y git sudo openssl  unzip libmcrypt-dev  libzip-dev libxml2-dev libonig-dev  \
    libmagickwand-dev --no-install-recommends
RUN pecl install mcrypt-1.0.4
RUN docker-php-ext-enable mcrypt
        
RUN docker-php-ext-install gd  tokenizer xml pdo mbstring pdo_mysql

COPY ./docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint
RUN chmod +x /usr/local/bin/docker-php-entrypoint

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# run as non root user
# ARG USER=lumen
# ARG UID=1000
# ARG GID=1000
# ARG PW=docker
# RUN useradd -m ${USER} --uid=${UID} && echo "${USER}:${PW}" | \
#  chpasswd

# RUN chown root:root /usr/bin/sudo && chmod 4755 /usr/bin/sudo
# RUN chmod 644 /usr/lib/sudo/sudoers.so
# RUN chown -R root /usr/lib/sudo
# RUN chown -R root:root /etc/sudoers
# RUN chown -R root:root /etc/sudoers.d
# RUN echo 'lumen ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

# USER ${UID}:${GID}