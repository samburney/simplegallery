FROM lsiobase/ubuntu:xenial

LABEL maintainer 'Sam Burney <sam@burney.io>'

# Disable dpkg frontend to avoid error messages
ENV DEBIAN_FRONTEND=noninteractive

# Set default environment vars
ENV PHP_UPLOAD_MAX_FILESIZE 2048M
ENV PHP_POST_MAX_SIZE 2048M
ENV PHP_MEMORY_LIMIT 256M
ENV DB_DRIVER mysql
ENV DB_HOST localhost
ENV DB_DATABASE simplegallery
ENV DB_USERNAME root
ENV DB_PASSWORD ''

# Install wget and install/updates certificates
RUN apt-get update \
    && apt-get install -f -y -q --no-install-recommends \
        apache2 \
        php \
        libapache2-mod-php \
        php-mysql \
        php-gd \
        php-mbstring \
        php-mcrypt \
        php-zip \
        php-xml \
        php-bcmath \
        php-curl \
        php-gmp \
        git \
        mysql-client \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

# Set up application
RUN git clone https://github.com/samburney/simplegallery.git /var/www/simplegallery
ADD ./docker/archive/simplegallery-vendor-20170102.tbz2 /var/www/simplegallery/
ADD ./docker/archive/simplegallery-bower-20170102.tbz2 /var/www/simplegallery/
RUN chown -R www-data:www-data /var/www/simplegallery/ \
    && chmod -R 664 /var/www/simplegallery/ \
    && find /var/www/simplegallery/ -type d -exec chmod 2775 {} \;

COPY ./docker/root/ /

RUN ln -sf /dev/stdout /var/log/apache2/access.log \
    && ln -sf /dev/stderr /var/log/apache2/error.log \
    && a2enmod rewrite

VOLUME ["/files"]
EXPOSE 80

ENTRYPOINT ["/init"]
