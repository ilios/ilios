#!/bin/sh
PUPPET_MDOULE_DIR=/etc/puppetlabs/code/modules

if [ ! -d "$PUPPET_MDOULE_DIR" ]; then
  mkdir -p "$PUPPET_MDOULE_DIR"
fi

#Copy local modules
echo "Copying local modules to puppet dir..."
cp -r /vagrant/provision/puppet/modules/* "$PUPPET_MDOULE_DIR"

PUPPET_ENVIRONMENT_DIR=/etc/puppetlabs/code/environments

if [ ! -d "PUPPET_ENVIRONMENT_DIR" ]; then
  mkdir -p "PUPPET_ENVIRONMENT_DIR"
fi

#Copy local environments
echo "Copying local environments to puppet dir..."
cp -r /vagrant/provision/puppet/environments/* "$PUPPET_ENVIRONMENT_DIR"
