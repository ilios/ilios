class profile::build::symfony {
    fooacl::conf { 'symfony-var':
        target => [
            '/vagrant/sfi/var/cache',
            '/vagrant/sfi/var/logs',
        ],
        permissions => [
            'user:vagrant:rwX',
            'user:www-data:rwX',
        ],
    }

    class {'ruby':
        gems_version => 'latest',
    }

    $gems = ['sass', 'bourbon', 'neat', 'bitters', 'refills']

    package { $gems:
        ensure => present,
        provider => 'gem',
        require => Class['ruby']
    }
}
