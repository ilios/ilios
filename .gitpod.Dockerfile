FROM gitpod/workspace-full

RUN PHP_INI=`php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"` && sudo install-packages php-pear php-ldap && sudo pear config-set php_ini ${PHP_INI} && sudo pecl channel-update pecl.php.net && sudo pecl install apcu && echo "extension=apcu.so" | sudo tee -a ${PHP_INI}
