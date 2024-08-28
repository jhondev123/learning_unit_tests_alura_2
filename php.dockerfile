# Use a imagem oficial do PHP 7.3 com PHP-FPM
FROM php:7.3-fpm

# Instale dependências necessárias
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libzip-dev \
    libsqlite3-dev \
    zlib1g-dev \
    && docker-php-ext-install -j$(nproc) gd \ 
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite


# Instale o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Copie os arquivos do projeto para o contêiner
COPY . /var/www/html

# Rode o composer install
RUN composer install

# Exponha a porta 9000 para o PHP-FPM
EXPOSE 9000

# Inicie o PHP-FPM
CMD ["php-fpm"]
