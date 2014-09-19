class profile::phpmyadmin (
) {
    include profile::common::apache
    include profile::common::mysql
    include profile::common::php

    package {'phpmyadmin':
        ensure     => latest,
        require    => Class['::mysql::server']
    }

    file {'/etc/phpmyadmin/config.inc.php':
        content => "<?php\n\$cfg['blowfish_secret'] = 'notsecret';\n\$cfg['Servers'][1]['auth_type'] = 'config';\n\$cfg['Servers'][1]['user'] = 'admin';\n\$cfg['Servers'][1]['password'] = 'admin';",
        require => Package['phpmyadmin']
    }

    apache::vhost { 'ssl-ilios.phpmyadmin.dev':
        default_vhost => true,
        port => 443,
        priority => '003',
        ssl => true,
        docroot => '/usr/share/phpmyadmin',
        directoryindex => 'index.php',
        servername => "ilios.phpmyadmin.dev",
        override => 'all',
        directories => [
            {
                path => '/usr/share/phpmyadmin',
                options => [
                    'Indexes',
                    'FollowSymLinks',
                    'MultiViews'
                ],
                require => 'all granted'
            }
        ],
        ssl_cert => '/etc/ssl/certs/ssl-cert-snakeoil.pem',
        ssl_key => '/etc/ssl/private/ssl-cert-snakeoil.key',
        access_log_file => "ssl-ilios.phpmyadmin.dev_access.log",
        error_log_file => "ssl-ilios.phpmyadmin.dev_error.log",
        require => [Package['phpmyadmin'], Class['apache']]
    }

    apache::vhost { 'ilios.phpmyadmin.dev':
        port => 80,
        priority => '004',
        docroot => '/usr/share/phpmyadmin',
        directoryindex => 'index.php',
        servername => "ilios.phpmyadmin.dev",
        override => 'all',
        directories => [
            {
                path => '/usr/share/phpmyadmin',
                options => [
                    'Indexes',
                    'FollowSymLinks',
                    'MultiViews'
                ],
                require => 'all granted'
            }
        ],
        access_log_file => "ilios.phpmyadmin.dev_access.log",
        error_log_file => "ilios.phpmyadmin.dev_error.log",
        require => [Package['phpmyadmin'], Class['apache']]
    }
}
