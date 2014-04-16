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

  $users = {
    'vagrant@localhost' => {
      ensure        => 'present'
    },
    "${dbuser}@localhost" => {
      ensure        => 'present',
      password_hash => mysql_password($dbpass)
    },
  }
  $grants = {
    'vagrant@localhost/*.*' => {
      ensure     => 'present',
      options    => ['GRANT'],
      privileges => ['ALL'],
      table      => '*.*',
      user       => 'vagrant@localhost',
    },
    "${dbuser}@localhost/*.*" => {
      ensure     => 'present',
      options    => ['GRANT'],
      privileges => ['ALL'],
      table      => "${dbname}.*",
      user       => "${dbuser}@localhost",
    },
  }
  class { '::mysql::server': 
    users    => $users,
    grants  => $grants
  }
  class { '::mysql::bindings': 
    php_enable    => true
  }

  class { 'apache':
    default_mods        => false,
    default_vhost       => false,
    default_confd_files => false,
    mpm_module => 'prefork'
  }
  class {'apache::mod::php': }
  class {'apache::mod::rewrite': }
  class {'apache::mod::headers': }
  class {'apache::mod::setenvif': }
  

   package { "php5-ldap":
    ensure  => present,
    require => Class['apache::mod::php']
  }

  apache::vhost { 'iliosdev':
    default_vhost   => true,
    docroot         => $docroot,
    docroot_owner   => 'vagrant',
    docroot_group   => 'vagrant',
    override        => ['all'],
    ssl             => true,
    port            => '443',
    ip              => '*'
  }

  package { "sendmail":
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

  file {"${docroot}/application/config/config.php":
    source => "${docroot}/application/config/default.config.php",
  }

  exec {"set-version":
    cwd => "/tmp",
    command => "/bin/date +%s > ilios_version.txt",
  }

  exec {"edit-index.php":
    cwd => "${docroot}/",
    require => Exec["set-version"],
    command => '/bin/sed "s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.index.php > index.php',
  }

  exec {"edit-ilios.php":
    cwd => "${docroot}/application/config/",
    require => Exec["set-version"],
    command => '/bin/sed "s/%%ILIOS_INSTITUTION_NAME%%/Sweet Valley University/; s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.ilios.php > ilios.php',
  }

  exec {"edit-database.php":
    cwd => "${docroot}/application/config/",
    command => "/bin/sed 's/%%DBGROUP%%/default/;  s/%%DBHOSTNAME%%/localhost/; s/%%DBUSERNAME%%/${dbuser}/; s/%%DBPASSWORD%%/${dbpass}/; s/%%DBNAME%%/${dbname}/' default.database.php > database.php",
  }

}

class{'ilios': }