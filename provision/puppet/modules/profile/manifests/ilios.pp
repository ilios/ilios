class profile::ilios (
    $user = 'vagrant',
    $docroot = '/var/www/ilios3',
    $docroot_target = '/vagrant/web',
    $fqdn         = 'ilios.dev'
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
        host_aliases => ['ilios']
    }

    apache::vhost { 'ilios.dev':
        default_vhost   => true,
        docroot => $docroot,
        servername => "${fqdn}",
        override => 'all',
        port => '80',
        ip => '*',
        require => [File[$docroot]],
        directories => [
            { 'path'     => '/',
              'provider' => 'location',
              'auth_type'     => 'shibboleth',
              'require'     => 'shibboleth',
             },
             { 'path'     => '/var/www/ilios3',
               'provider' => 'directory',
               'options'     => 'FollowSymLinks MultiViews',
               'allow_override'     => 'all',
               'require'     => 'all granted',
              },
        ],
    }
}
