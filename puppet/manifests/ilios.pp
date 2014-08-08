class ilios (
  $docroot=$options::docroot,
  $apacheuser=$options::apacheuser,
  $repodir=$options::repodir,
  $dbuser=$options::dbuser,
  $dbpass=$options::dbpass,
  $dbname=$options::dbname,
  $adminemail=$options::adminemail,
) inherits options {
  exec { "update_upgrade":
    command => "/usr/bin/apt-get update; /usr/bin/apt-get upgrade -y"
  }

  Exec["update_upgrade"] -> Package <| |>

  $basepackages = [ "screen", "emacs", "vim", "wget", "curl" ]
  package { $basepackages:
    ensure => latest,
  }
  file {$docroot:
      ensure    => 'link',
      target    => '/vagrant/web'
  }

  $users = {
    'admin@localhost' => {
      ensure        => 'present',
      password_hash => mysql_password('admin'),
    },
    'vagrant@%' => {
      ensure        => 'present'
    },
    "${dbuser}@localhost" => {
      ensure        => 'present',
      password_hash => mysql_password($dbpass)
    },
  }
  $grants = {
    'vagrant/*.*' => {
        ensure     => 'present',
        options    => ['GRANT', 'WITH GRANT OPTION'],
        privileges => ['ALL'],
        table      => '*.*',
        user       => 'vagrant@%',
    },
    'admin@localhost/*.*' => {
      ensure     => 'present',
      options    => ['GRANT', 'WITH GRANT OPTION'],
      privileges => ['ALL'],
      table      => '*.*',
      user       => 'admin@localhost',
    },
    "${dbuser}@localhost/*.*" => {
      ensure     => 'present',
      options    => ['GRANT', 'WITH GRANT OPTION'],
      privileges => ['ALL'],
      table      => "${dbname}.*",
      user       => "${dbuser}@localhost",
    },
  }
  class { '::mysql::server':
    users            => $users,
    grants           => $grants,
    override_options => { 'mysqld' => { 'bind_address' => '0.0.0.0' } }
  }

  class { '::mysql::bindings':
    php_enable    => true
  }

  class { 'apache':
    default_mods        => false,
    default_vhost       => false,
    default_confd_files => false,
    mpm_module          => 'prefork',
    user                => 'vagrant',
    group               => 'vagrant',
  }
  class {'apache::mod::php': }
  class {'apache::mod::rewrite': }
  class {'apache::mod::headers': }
  class {'apache::mod::setenvif': }

  package { "php5-ldap":
    ensure  => present,
    require => Class['apache::mod::php']
  }


  $_phpAdminValues = [
    'memory_limit 768M',
    'upload_max_filesize 128M',
    'post_max_size 128M',
    'apc.rfc1867 on',
    'date.timezone UTC',
    'short_open_tag off',
    'html_errors on'
  ]

  apache::vhost { 'iliosdev':
    default_vhost   => true,
    docroot         => $docroot,
    docroot_owner   => 'vagrant',
    docroot_group   => 'vagrant',
    override        => ['all'],
    ssl             => true,
    port            => '443',
    ip              => '*',
    php_admin_values    => $_phpAdminValues,
    aliases             =>
        {
            alias      => '/phpmyadmin',
            path       => '/usr/share/phpmyadmin',
        },
    require        => [File[$docroot]]
  }

  package { "sendmail":
    ensure => present,
  }

  package { "php5-intl":
    ensure => present,
  }

  package { "expect":
    ensure => present,
  }

  exec {"create-db":
    cwd => "${repodir}/database/install",
    unless => "/usr/bin/sudo /bin/ls /var/lib/mysql/${dbname}/mesh_concept_x_term.MYI",
    command => "/bin/sed 's/XXXXXX/${dbname}/g' make_new_ilios_database.sql > /tmp/new.sql && /usr/bin/mysql -uroot < /tmp/new.sql && /usr/bin/expect user_zero.exp ${dbname} ${dbuser} ${dbpass} ${adminemail}",
    require => [Class["::mysql::server"],Package["expect"]],
  }

  exec {"edit-config.php":
    require   => [File[$docroot]],
    cwd => "${docroot}/application/config/",
    command => '/bin/sed "s/%%ENCRYPTION_KEY%%/TEST_KEY/" default.config.php > config.php',
  }

  exec {"set-version":
    cwd => "/tmp",
    command => "/bin/date +%s > ilios_version.txt",
  }

  exec {"edit-index.php":
    cwd => "${docroot}/",
    require => [Exec["set-version"],File[$docroot]],
    command => '/bin/sed "s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.index.php > index.php',
  }

  exec {"edit-ilios.php":
    cwd => "${docroot}/application/config/",
    require => [Exec["set-version"],File[$docroot]],
    command => '/bin/sed "s/%%ILIOS_INSTITUTION_NAME%%/Sweet Valley University/; s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.ilios.php > ilios.php',
  }

  exec {"edit-database.php":
    cwd     => "${docroot}/application/config/",
    command => "/bin/sed 's/%%DBGROUP%%/default/;  s/%%DBHOSTNAME%%/localhost/; s/%%DBUSERNAME%%/${dbuser}/; s/%%DBPASSWORD%%/${dbpass}/; s/%%DBNAME%%/${dbname}/' default.database.php > database.php",
    require => [File[$docroot]],
  }

  package {'phpmyadmin':
    ensure     => latest,
    require    => Class['::mysql::server']
}

  package {'php-apc':
    ensure     => latest
}

  package {'php5-xdebug':
    ensure     => latest
  }

  file {'/etc/phpmyadmin/config.inc.php':
    content => "<?php\n\$cfg['blowfish_secret'] = 'notsecret';\n\$cfg['Servers'][1]['auth_type'] = 'config';\n\$cfg['Servers'][1]['user'] = 'admin';\n\$cfg['Servers'][1]['password'] = 'admin';",
    require => Package['phpmyadmin']
  }

  class { 'nodejs':
    manage_repo => true,
}

  package { 'build-essential':
    ensure  => present
}

  package { 'git':
    ensure  => present
  }

  $devNodePackages = ['bower', 'ember-precompile']
  package { $devNodePackages:
    ensure   => present,
    provider => 'npm',
    require  => [Class['nodejs'], Package['build-essential']],
  }
  class { 'ruby':
    gems_version  => 'latest'
  }
  $devRubyGems = ['sass', 'bourbon', 'neat', 'bitters', 'refills']
  package { $devRubyGems:
    ensure   => present,
    provider => 'gem',
    require  => [Class['ruby']],
  }

}

class{'ilios': }
