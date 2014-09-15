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
}
