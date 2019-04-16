FROM ircmaxell/php-compiler:16.04-dev

COPY ./ /compiler

WORKDIR /compiler

RUN composer install --no-ansi --no-dev --no-interaction --no-progress --no-scripts --optimize-autoloader

ENTRYPOINT ["php", "/compiler/bin/jit.php"]

CMD ["-r", "echo \"Hello World\n\";"]
