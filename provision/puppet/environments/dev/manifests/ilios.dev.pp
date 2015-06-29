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

    class { ['profile::ilios', 'profile::build::symfony', 'profile::shibbolethsp']: }

    Php::Extension <| |>
    # Configure extensions
    -> Php::Config <| |>
}
