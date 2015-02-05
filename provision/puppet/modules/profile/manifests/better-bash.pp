class profile::better-bash {
    include git

    exec { "clone_bash_it":
        cwd => '/home/vagrant/',
        command => 'git clone https://github.com/revans/bash-it.git /home/vagrant/.bash_it',
        user => 'vagrant',
        creates => '/home/vagrant/.bash_it/',
        path => ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin']
    } ->

    exec { 'install_candy_alt':
        cwd => '/home/vagrant/',
        command => "git clone https://github.com/vpassapera/candy-alt.git /home/vagrant/.bash_it/themes/candy-alt",
        user => 'vagrant',
        creates => '/home/vagrant/.bash_it/themes/candy-alt',
        path => ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin'],
        require => Exec['clone_bash_it']
    }

    exec { 'clone_bash_config':
        cwd => '/home/vagrant/',
        command => "git clone https://github.com/vpassapera/BashConfigs.git /home/vagrant/.bash",
        user => 'vagrant',
        creates => '/home/vagrant/.bash/',
        path => ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin'],
        require => Exec['install_candy_alt']
    }

    exec { 'clone_nano_highlight':
        cwd => '/home/vagrant/',
        command => "git clone https://github.com/serialhex/nano-highlight.git /home/vagrant/.nano",
        user => 'vagrant',
        creates => '/home/vagrant/.nano/',
        path => ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin']
    }

    exec { "install_bash_it":
        command => "bash -c '/home/vagrant/.bash/install.sh'",
        user => 'vagrant',
        group => 'vagrant',
        path => ['/usr/bin', '/usr/sbin', '/usr/local/bin', '/bin'],
        require => Exec['clone_bash_config']
    }

    file { '/home/vagrant/.nanorc':
        owner => 'vagrant',
        group => 'vagrant',
        mode => 0644,
        content => template('profile/nanorc.erb'),
        require => Exec['clone_nano_highlight', 'install_bash_it']
    }

    notice ("Better bash installed. Might have to run \"source ~/.bashrc\"")
}
