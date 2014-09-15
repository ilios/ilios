class profile::common::apache (
    $user = 'vagrant',
    $group = $user,
    $logs = '/home/vagrant/logs/apache'
) {
    class { '::apache':
        default_vhost => false,
        default_confd_files => false,
        mpm_module => 'prefork',
        user => $user,
        group => $group
    }

    class { 'apache::mod::ssl':
        ssl_compression => false,
        ssl_options => ['StdEnvVars']
    }

    class { ['apache::mod::rewrite', 'apache::mod::headers', 'apache::mod::php']: }

    file { $logs:
        ensure => 'link',
        target => '/var/log/apache2',
        owner => $user,
        group => $group,
        mode => 0777
    }
}
