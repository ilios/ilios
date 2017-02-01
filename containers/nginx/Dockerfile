FROM iamjrjohnson/ilios-src:latest

MAINTAINER Ilios Project Team <support@iliosproject.org>

RUN apk add --update nginx
RUN rm -rf /var/cache/apk/* && rm -rf /tmp/*

ADD nginx.conf /etc/nginx/
ADD ilios.conf /etc/nginx/conf.d/default.conf

RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

CMD ["nginx"]

EXPOSE 80
EXPOSE 443
