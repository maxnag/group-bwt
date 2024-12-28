# vim:set ft=dockerfile:
FROM debian:bookworm-slim

LABEL org.opencontainers.image.authors="Max Nagaychenko <nagaychenko.dev@gmail.com>"
LABEL org.opencontainers.image.source=https://github.com/maxnag/group-bwt
LABEL org.opencontainers.image.description="PHP image"

# Arguments defined in docker-compose.yml
ARG DEBUG_HOST_IP

RUN apt-get update \
    && apt-get -y upgrade \
    && apt-get -y install apt-utils mc netcat-openbsd vim curl wget lsb-release gpg supervisor procps apt-transport-https ca-certificates \
    && mkdir -p /run/php \
    && mkdir -p /var/www \
    && chmod -R 0755 /var/www \
    && chown -R www-data:www-data /var/www \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" |  tee /etc/apt/sources.list.d/php.list \
    && apt-get update \
    && apt-get -y upgrade \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get -y install git nodejs \
    && npm install --global npm@latest \
    && npm install --global prettier @prettier/plugin-php \
    && apt-get -y install php8.3 php8.3-cli php8.3-common php8.3-curl php8.3-gd php8.3-mbstring php8.3-mysql php8.3-opcache php8.3-readline php8.3-xml php8.3-fpm php8.3-bcmath php8.3-intl php8.3-xmlrpc php8.3-xsl php8.3-zip php8.3-memcached php8.3-xdebug php8.3-phpdbg php-pear graphviz \
    # if you need xhprof uncomment 2 lines below
    && apt-get -y install apt php8.3-xhprof \
    && echo "xhprof.output_dir=/tmp/xhprof" >> /etc/php/8.3/mods-available/xhprof.ini \
    && apt-get -y install mariadb-client libsqlite3-dev libsqlite3-0 telnet \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer  \
    && echo "zend_extension=$(find /usr/lib/php/20230831 -name xdebug.so)" > /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.mode=profile,debug,develop,coverage" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.output_dir = /tmp/xdebug/" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.client_port=9003" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.client_host=$DEBUG_HOST_IP" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.log_level=0" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.profiler_output_name=cachegrind.out.%p.log" >> /etc/php/8.3/mods-available/xdebug.ini \
    && echo "xdebug.file_link_format=phpstorm://open?%f:%l" >> /etc/php/8.3/mods-available/xdebug.ini \
    && mkdir /tmp/xdebug/ \
    && mkdir /tmp/xhprof/ \
    && apt-get autoremove -y \
    && apt-get clean -y \
    && apt-get autoclean -y \
    && rm -rf /var/lib/apt/lists/*

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini php-fpm.conf /etc/php/8.3/fpm/
RUN rm -Rf /etc/cron.daily  && \
    rm -Rf /etc/cron.weekly && \
    rm -Rf /etc/cron.monthly && \
    rm -Rf /etc/cron.hourly

# set timezone
RUN ln -fs /usr/share/zoneinfo/Europe/Kiev /etc/localtime && dpkg-reconfigure --frontend noninteractive tzdata

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
