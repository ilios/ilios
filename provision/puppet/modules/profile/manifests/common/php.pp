class profile::common::php (
    $user = 'vagrant',
    $group = $user,
    $logs = '/home/vagrant/logs/php'
) {
    include ['php', 'php::params']#, 'php::apache']
    $apis = [
        'php::cli',
        'php::dev'
    ]
    $extensions = [
        'php::pear',
        'php::extension::curl',
        'php::extension::intl',
        'php::extension::mysql',
        'php::extension::sqlite',
        'php::extension::memcached',
        'php::extension::gd',
        'php::extension::imagick',
        'php::extension::mcrypt',
        'php::extension::xdebug',
        'php::extension::ldap',
        'php::composer'
    ]

    file { $logs:
        ensure => directory,
        owner => $user,
        group => $group,
        mode => 0777
    } ->

    class { $apis: }
    class { $extensions: }

    file { '/etc/php5/apache2/conf.d/20-mcrypt.ini':
        ensure => 'link',
        target => '/etc/php5/mods-available/mcrypt.ini',
        require => Class['php::extension::mcrypt']
    }

    php::extension { 'php5-xmlrpc':
        ensure => installed,
        package => 'php5-xmlrpc',
        provider => 'apt'
    }

    php::extension { 'php5-json':
        ensure => installed,
        package => 'php5-json',
        provider => 'apt'
    }

    php::apache::config { 'memory_limit':
        setting => 'memory_limit',
        value => '768M',
        file => '/etc/php5/apache2/php.ini'
    }

    php::apache::config { 'upload_max_filesize':
        setting => 'upload_max_filesize',
        value => '128M'
    }

    php::apache::config { 'post_max_size':
        setting => 'post_max_size',
        value => '128M'
    }

    php::apache::config { 'apc.rfc1867':
        setting => 'apc.rfc1867',
        value => 'On'
    } #Shouldn't be using APC any more...

    php::apache::config { 'date.timezone':
        setting => 'date.timezone',
        value => 'UTC'
    }

    php::apache::config { 'short_open_tag':
        setting => 'short_open_tag',
        value => 'Off'
    }

    php::apache::config { 'html_errors':
        setting => 'html_errors',
        value => 'On'
    }

    php::config { 'cli_memory_limit':
        setting => 'memory_limit',
        value => '768M',
        file => '/etc/php5/cli/php.ini'
    }

    php::config { "apache_log_errors":
        setting => "log_errors",
        value => "On",
        file => '/etc/php5/apache2/php.ini'
    }

    php::config { "apache_error_log":
        setting => "error_log",
        value => "${logs}/php_apache.log",
        file => '/etc/php5/apache2/php.ini'
    }

    php::config { "cli_log_errors":
        setting => "log_errors",
        value => "On",
        file => '/etc/php5/cli/php.ini'
    }

    php::config { "cli_error_log":
        setting => "error_log",
        value => "${logs}/php_cli.log",
        file => '/etc/php5/cli/php.ini'
    }

    php::config { 'cli-date.timezone':
        setting => 'date.timezone',
        value => 'UTC',
        file => '/etc/php5/cli/php.ini'
    }

}
