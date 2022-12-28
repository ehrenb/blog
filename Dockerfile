FROM nginx:alpine

FROM php:7.4-fpm

ENV DEBIAN_FRONTEND noninteractive

RUN apt update &&\
    apt install -y nginx

COPY nginx.conf /etc/nginx/sites-enabled/default
COPY entrypoint.sh /etc/entrypoint.sh
RUN chmod +x /etc/entrypoint.sh

COPY blog /var/www/blog

ENTRYPOINT ["/etc/entrypoint.sh"]