FROM php:8.3-cli-alpine
WORKDIR /app
COPY . /app
RUN mkdir -p /app/var && chown -R www-data:www-data /app/var
USER www-data
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/router.php"]
