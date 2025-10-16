FROM php:8.3-apache

# ติดตั้ง Apache SSL Module
RUN set -eux; \
  apt-get update && apt-get install -y ssl-cert; \
  a2enmod rewrite ssl; \
  rm -rf /var/lib/apt/lists/*

# ติดตั้ง PHP Extensions และ MongoDB
RUN apt-get update && apt-get install -y \
  zlib1g-dev libpng-dev libzip-dev libfreetype6-dev libjpeg62-turbo-dev libssl-dev pkg-config libedit-dev zip \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && docker-php-ext-install mysqli \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd zip pdo_mysql \
  && rm -rf /var/lib/apt/lists/*

# ตั้งค่า PHP.INI
RUN set -eux; \
  cd $PHP_INI_DIR; \
  cp php.ini-production php.ini; \
  echo "date.timezone=Asia/Bangkok"          > conf.d/date_timezone.ini