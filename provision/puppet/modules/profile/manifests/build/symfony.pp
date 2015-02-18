class profile::build::symfony {
    fooacl::conf { 'symfony-var':
        target => [
            '/vagrant/var/cache',
            '/vagrant/var/logs',
        ],
        permissions => [
            'user:vagrant:rwX',
            'user:www-data:rwX',
        ],
    }
}
