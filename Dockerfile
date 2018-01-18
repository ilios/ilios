FROM alpine:3.7

MAINTAINER Ilios Project Team <support@iliosproject.org>

COPY . /var/www/ilios
RUN mkdir -p \
    /var/www/ilios/var \
    /var/www/ilios/var/cache \
    /var/www/ilios/var/logs \
    /var/www/ilios/var/session \
    /var/www/ilios/var/tmp \
    /var/www/ilios/vendor && \
    chown -R nobody:nobody /var/www/ilios

CMD /bin/true
