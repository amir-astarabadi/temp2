FROM nginx:stable-alpine

ENV NGINX_USER=qanun_api
ENV NGINX_GROUP=qanun_api

RUN mkdir -p /var/www/html/public

RUN mkdir -p /var/log/nginx/qanun_api

COPY default.conf /etc/nginx/conf.d/default.conf

RUN sed -i "s/user www-data /user ${NGINX_USER}/g" /etc/nginx/nginx.conf

RUN adduser -g ${NGINX_GROUP} -s /bin/sh -D ${NGINX_USER}  