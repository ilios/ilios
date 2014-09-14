class profile::git (
    $user = 'vagrant',
    $email = 'vagrant@localhost',
    $user_name = 'Vagrant Machine',
) {
    include git

    git::resource::config { $user:
        email => $email,
        realname => $user_name
    }
}
