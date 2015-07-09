class profile::common::php (
    $user = 'vagrant',
    $group = $user,
    $logs = '/home/vagrant/logs/php',
    $xdebug_remote_port = 9000,
    $xdebug_idekey = 'PHPSTORM',
    $profiler_on  = false,
    $profiler_dir = '/home/vagrant/profiler'
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
        'php::extension::ldap',
        'php::composer'
    ]

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

    php::extension { 'php5-xsl':
        ensure => installed,
        package => 'php5-xsl',
        provider => 'apt'
    }

    php::extension { 'php5-xdebug':
        ensure => installed,
        package => 'php5-xdebug',
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

    php::config { 'xdebug.remote_enable' :
        setting => 'xdebug.remote_enable',
        value => '1',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.remote_connect_back' :
        setting => 'xdebug.remote_connect_back',
        value => '1',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.remote_host' :
        setting => 'xdebug.remote_host',
        value => '127.0.0.1',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.remote_port' :
        setting => 'xdebug.remote_port',
        value => '9000',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.trace_enable_trigger' :
        setting => 'xdebug.trace_enable_trigger',
        value => '1',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.trace_output_dir' :
        setting => 'xdebug.trace_output_dir',
        value => 'home/vagrant/trace',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.profiler_enable_trigger' :
        setting => 'xdebug.profiler_enable_trigger',
        value => '1',
        file => '/etc/php5/mods-available/xdebug.ini'
    }

    php::config { 'xdebug.profiler_output_dir' :
        setting => 'xdebug.profiler_output_dir',
        value => '/home/vagrant/profiler/',
        file => '/etc/php5/mods-available/xdebug.ini'
    }
}
