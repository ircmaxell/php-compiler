FROM ircmaxell/php-compiler:16.04-dev

WORKDIR ../

RUN curl -L https://github.com/ircmaxell/php-compiler/archive/master.zip -o master.zip && \
    unzip master.zip && \
    mv php-compiler-master/* compiler/

WORKDIR compiler

RUN php /composer.phar install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

ENTRYPOINT ["php", "/compiler/bin/jit.php"]

CMD ["-r", "'echo \"Hello World\n\";'"]