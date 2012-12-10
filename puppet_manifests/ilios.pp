class ilios (
  $docroot=$params::docroot,
  $apacheuser=$params::apacheuser,
  $repodir=$params::repodir,
  $dbuser=$params::dbuser,
  $dbpass=$params::dbpass,
  $dbname=$params::dbname,
  $adminemail=$params::adminemail,
) inherits params {

  exec { "apt-get update":
    command => "/usr/bin/apt-get update",
  }

  package { "mysql-server":
    ensure => present,
    require => Exec["apt-get update"],
  }

  package { "apache2":
    ensure => present,
  }

  package { "libapache2-mod-php5":
    ensure => present,
    notify => Service["apache2"],
  }

  package { "php5-mysql":
    ensure => present,
    require => Package["libapache2-mod-php5"],
    notify => Service["apache2"],
  }

  package { "php5-ldap":
    ensure => present,
    require => Package["libapache2-mod-php5"],
    notify => Service["apache2"],
  }

  package { "sendmail":
    ensure => present,
  }

  package { "expect":
    ensure => present,
  }

  service { "mysql":
    ensure => running,
    enable => true,
    require => Package["mysql-server"],
  }

  service { "apache2":
    ensure => running,
    enable => true,
    require => Package["apache2"],
  }

   exec {"add-mod-rewrite":
    command => "/usr/sbin/a2enmod rewrite",
    notify => Service["apache2"],
    require => Package["apache2"],
  }

  exec {"create-db":
    cwd => "${repodir}/database/install",
    unless => "/usr/bin/sudo /bin/ls /var/lib/mysql/${dbname}/mesh_concept_x_term.MYI",
    command => "/bin/sed 's/XXXXXX/${dbname}/g' make_new_i2_database.sql > /tmp/new.sql && /usr/bin/mysql -uroot < /tmp/new.sql && /usr/bin/mysql -uroot -e \"GRANT ALL ON ${dbname}.* TO '${dbuser}'@'localhost' identified by '${dbpass}';\" && /usr/bin/expect user_zero.exp ${dbname} ${dbuser} ${dbpass} ${adminemail}",
    require => [Service["mysql"],Package["expect"]],
  }

  exec {"allow-override":
    cwd => "/etc/apache2/sites-enabled",
    command => "/usr/bin/find . -! -type d -exec /bin/sed -i '/<Directory \\/var\\/www\\/>/,/<\\/Directory>/ { s#AllowOverride None#AllowOverride All#}' {} \\;",
    notify => Service["apache2"],
    require => Exec["link-ssl-conf-file"],
  }

  exec {"enable-mod-ssl":
    command => "/usr/sbin/a2enmod ssl",
    require => Package["apache2"],
  }

  exec {"link-ssl-conf-file":
    unless => "/bin/ls /etc/apache2/sites-enabled/000-default-ssl",
    command => "/bin/ln -s /etc/apache2/sites-available/default-ssl /etc/apache2/sites-enabled/000-default-ssl",
    notify => Service["apache2"],
    require => Exec["enable-mod-ssl"],
  }

  file {"${docroot}/system/application/config/config.php":
    source => "${docroot}/system/application/config/default.config.php",
  }

  file {"${docroot}/system/application/config/ilios.php":
    source => "${docroot}/system/application/config/default.ilios.php"
  }

  file {"${docroot}/system/application/config/database.php":
    source => "${docroot}/system/application/config/default.database.php"
  }

  file {"${docroot}/index.html":
    ensure => "absent",
    require => Service["apache2"],
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
    cwd => "${docroot}/system/application/config/",
    require => Exec["set-version"],
    command => '/bin/sed "s/%%ILIOS_INSTITUTION_NAME%%/Sweet Valley University/; s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.ilios.php > ilios.php',
  }

  exec {"edit-config.php":
    cwd => "${docroot}/system/application/config/",
    command => '/bin/sed "s#https://%%DEPLOY_URL%%/#/#" default.config.php > config.php',
  }

  exec {"edit-database.php":
    cwd => "${docroot}/system/application/config/",
    command => "/bin/sed 's/%%DBGROUP%%/default/;  s/%%DBHOSTNAME%%/localhost/; s/%%DBUSERNAME%%/${dbuser}/; s/%%DBPASSWORD%%/${dbpass}/; s/%%DBNAME%%/${dbname}/' default.database.php > database.php",
  }

}

include ilios
