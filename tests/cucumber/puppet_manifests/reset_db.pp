class reset_db (
  $docroot=$params::docroot,
  $apacheuser=$params::apacheuser,
  $repodir=$params::repodir,
  $dbuser=$params::dbuser,
  $dbpass=$params::dbpass,
  $dbname=$params::dbname,
  $adminemail=$params::adminemail,
) inherits params {

  package { "expect":
    ensure => present,
  }

  service { "mysql":
    ensure => running,
    enable => true,
  }

  exec {"drop-db":
    cwd => "${repodir}/database/install",
    command => "/usr/bin/mysql -uroot -e \"DROP DATABASE ${dbname};\"",
    require => [Service["mysql"],Package["expect"]],
  }

  exec {"create-db":
    cwd => "${repodir}/database/install",
    command => "/bin/sed 's/XXXXXX/${dbname}/g' make_new_ilios_database.sql > /tmp/new.sql && /usr/bin/mysql -uroot < /tmp/new.sql && /usr/bin/mysql -uroot -e \"GRANT ALL ON ${dbname}.* TO '${dbuser}'@'localhost' identified by '${dbpass}';\" && /usr/bin/expect user_zero.exp ${dbname} ${dbuser} ${dbpass} ${adminemail}",
    require => Exec["drop-db"],
  }

}

include reset_db