#!/bin/sh
PUPPET_MDOULE_DIR=/etc/puppet/modules

if [ ! -d "$PUPPET_MDOULE_DIR" ]; then
  mkdir -p "$PUPPET_MDOULE_DIR"
fi

#Copy local modules
echo "Copying local modules to puppet dir..."
cp -r /vagrant/provision/puppet/modules/* "$PUPPET_MDOULE_DIR"
