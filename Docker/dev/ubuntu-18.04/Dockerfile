FROM ubuntu:18.04

RUN mkdir compiler && \
    apt-get update && \
    apt-get -y install git build-essential gdb llvm-4.0-dev clang-4.0 unzip curl libcurl4-openssl-dev autoconf libssl-dev libgd-dev libzip-dev bison re2c libxml2-dev libsqlite3-dev libonig-dev vim clang nodejs

RUN curl -sL https://deb.nodesource.com/setup_10.x | bash - && \
    apt-get -y install nodejs

RUN curl -L https://github.com/php/php-src/archive/PHP-7.4.zip -o PHP-7.4.zip && unzip PHP-7.4.zip && mv php-src-PHP-7.4 php

WORKDIR php

RUN ./buildconf && \
    ./configure --disable-cgi --with-ffi --with-openssl --enable-mbstring --with-pcre-jit --with-zlib --enable-bcmath --with-curl --with-gd --enable-pcntl --enable-zip && \
    make -j16 && \
    make install

WORKDIR ../php-ast
RUN git clone https://github.com/nikic/php-ast . && \
    phpize && \
    ./configure && \
    make && \
    make install

COPY php.ini /usr/local/lib/php.ini

RUN curl --silent --show-error https://getcomposer.org/installer | php -- --no-ansi --install-dir=/usr/local/bin --filename=composer

WORKDIR ../compiler

ENV PHP="/usr/local/bin/php", PHP_7_4="/usr/local/bin/php", PHP_CS_FIXER_IGNORE_ENV="true", COMPOSER_ALLOW_SUPERUSER="1"

CMD ["/bin/bash"]
