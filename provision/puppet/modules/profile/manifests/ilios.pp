class profile::ilios (
    $user = 'vagrant',
    $docroot = '/var/www/ilios',
    $docroot_target = '/vagrant/web'
) {
    include profile::common::apache
    include profile::common::mysql
    include profile::common::php

    file { $docroot:
        ensure => 'link',
        target => $docroot_target
    }

    host { 'ilios.dev':
        ip => '127.0.0.1',
        host_aliases => ['ilios', 'www.ilios.dev', 'iliosdev']
    }

    apache::vhost { 'ssl-ilios.dev':
        default_vhost => true,
        port => 443,
        priority => '000',
        ssl => true,
        docroot => $docroot,
        directoryindex => 'index.php',
        servername => "${fqdn}",
        serveraliases => ["*.${fqdn}", 'iliosdev'],
        override => 'all',
        directories => [
            {
                path => $docroot,
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
        access_log_file => "ssl-${fqdn}_access.log",
        error_log_file => "ssl-${fqdn}_error.log",
        require => [File[$docroot]]
    }

    apache::vhost { 'ilios.dev':
        port => 80,
        priority => '001',
        docroot => $docroot,
        directoryindex => 'index.php',
        servername => "${fqdn}",
        serveraliases => ["*.${fqdn}", 'iliosdev'],
        override => 'all',
        directories => [
            {
                path => $docroot,
                options => [
                    'Indexes',
                    'FollowSymLinks',
                    'MultiViews'
                ],
                require => 'all granted'
            }
        ],
        access_log_file => "${fqdn}_access.log",
        error_log_file => "${fqdn}_error.log",
        require => [File[$docroot]]
    }
}
