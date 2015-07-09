#!/bin/sh
export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8
locale-gen en_US.UTF-8
dpkg-reconfigure locales

path="/vagrant/provision/shell"
"$path/bashrc.sh" #writes to the vagrant user's .bashrc
"$path/librarian.sh" #Installs librarian
"$path/local_modules.sh" #For modules that librarian can't install yet
"$path/puppet_apply.sh" #Tmp fix for https://github.com/mitchellh/vagrant/issues/3740
