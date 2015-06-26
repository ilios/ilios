class profile::shibbolethsp (
    $enabled = false,
    $configPath = '',
    $spCertPath = '',
    $spKeyPath = '',
    $shibbolethSpPackage = false
) {
    if $enabled {
        include profile::common::apache

        package {$shibbolethSpPackage:
            ensure     => latest,
            require    => Class['apache']
        }
        class{'apache::mod::shib': }

        service {'shibd':
            enable   => true,
            require  => [Class['apache::mod::shib'], Package[$shibbolethSpPackage]],
        }


        file { "/etc/shibboleth/shibboleth2.xml":
            ensure      => 'file',
            source      => $configPath,
            require     => Class['apache::mod::shib'],
            group       => 'root',
            owner       => 'root',
            notify      => Service['httpd','shibd'],
        }
        file { "/etc/shibboleth/sp-cert.pem":
            ensure      => 'file',
            source      => $spCertPath,
            require     => Class['apache::mod::shib'],
            group       => 'root',
            owner       => 'root',
            notify      => Service['httpd','shibd'],
        }
        file { "/etc/shibboleth/sp-key.pem":
            ensure      => 'file',
            source      => $spKeyPath,
            require     => Class['apache::mod::shib'],
            group       => 'root',
            owner       => 'root',
            notify      => Service['httpd','shibd'],
        }
    }
}
