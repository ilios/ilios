class profile::build::legacy (
    $docroot = '/vagrant/web',
    $dbuser = "ilios_user",
    $dbpass = "ilios_pass",
    $dbname = "ilios",
    $adminemail = "vagrant@ilios.dev"
) {
    file {$docroot:
        ensure => 'directory'
    }

    exec {"create-db":
        cwd => "/vagrant/database/install",
        unless => "/usr/bin/sudo /bin/ls /var/lib/mysql/${dbname}/mesh_concept_x_term.MYI",
        command => "/bin/sed \'s/XXXXXX/${dbname}/g\' make_new_ilios_database.sql > /tmp/new.sql && /usr/bin/mysql -uroot < /tmp/new.sql && /usr/bin/expect user_zero.exp ${dbname} ${dbuser} ${dbpass} ${adminemail}",
        require => [Class["::mysql::server"], Package["expect"]],
    }

    exec {"edit-config.php":
        cwd => "${docroot}/application/config/",
        command => '/bin/sed "s/%%ENCRYPTION_KEY%%/TEST_KEY/" default.config.php > config.php',
        require   => File[$docroot],
    }

    exec {"set-version":
        cwd => "/tmp",
        command => "/bin/date +%s > ilios_version.txt",
    }

    exec {"edit-index.php":
        cwd => "${docroot}/",
        require => [Exec["set-version"],File[$docroot]],
        command => '/bin/sed "s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.index.php > index.php'
    }

    exec {"edit-ilios.php":
        cwd => "${docroot}/application/config/",
        require => [Exec["set-version"],File[$docroot]],
        command => '/bin/sed "s/%%ILIOS_INSTITUTION_NAME%%/Sweet Valley University/; s/%%ILIOS_REVISION%%/`cat /tmp/ilios_version.txt`/" default.ilios.php > ilios.php',
    }

    exec {"edit-database.php":
        cwd     => "${docroot}/application/config/",
        command => "/bin/sed \'s/%%DBGROUP%%/default/;  s/%%DBHOSTNAME%%/localhost/; s/%%DBUSERNAME%%/${dbuser}/; s/%%DBPASSWORD%%/${dbpass}/; s/%%DBNAME%%/${dbname}/\' default.database.php > database.php",
        require   => File[$docroot]
    }
}
