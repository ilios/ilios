FROM alpine:3.5

MAINTAINER Ilios Project Team <support@iliosproject.org>

COPY . /var/www/ilios
RUN mkdir -p \
    /var/www/ilios/var \
    /var/www/ilios/var/cache \
    /var/www/ilios/var/logs \
    /var/www/ilios/var/session \
    /var/www/ilios/var/tmp

CMD /bin/true
