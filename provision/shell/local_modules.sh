#!/bin/sh
PUPPET_MDOULE_DIR=/etc/puppet/modules

if [ ! -d "$PUPPET_MDOULE_DIR" ]; then
  mkdir -p "$PUPPET_MDOULE_DIR"
fi

#Copy local modules
echo "Copying local modules to puppet dir..."
cp -r /vagrant/provision/puppet/modules/* "$PUPPET_MDOULE_DIR"

##Puppet modules that librarian won't install...PRs in some of them to address this
echo "Installing modules uninstallable by Librarian Puppet"
puppet module install nodes/php --ignore-dependencies --modulepath "$PUPPET_MDOULE_DIR" #Deps handled in Puppetfile
