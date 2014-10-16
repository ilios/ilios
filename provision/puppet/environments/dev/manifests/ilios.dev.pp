node 'ilios.dev' {
    $extra_packages = ['curl', 'screen', 'vim', 'wget', 'expect', 'sendmail', 'sqlite3']

    package { $extra_packages:
        ensure => installed
    }

    $logs = '/home/vagrant/logs'

    file { $logs:
        ensure => directory,
        owner => $user,
        group => $group,
        mode => 0777
    } ->

    class { ['profile::git', 'profile::better-bash', 'profile::ilios', 'profile::build::legacy', 'profile::build::symfony', 'profile::phpmyadmin']: }

    Php::Extension <| |>
    # Configure extensions
    -> Php::Config <| |>
}
