FROM debian:latest
RUN apt update
RUN apt install -y php8.2 php8.2-xml phpunit composer php8.2-curl php-mysql
