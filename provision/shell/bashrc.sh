#!/bin/sh
echo "export PHP_IDE_CONFIG='serverName=ilios.vagrant'" >> /home/vagrant/.bashrc
echo "export XDEBUG_CONFIG='idekey=PHPSTORM'" >> /home/vagrant/.bashrc

echo "Symlink puppet binarys into /usr/local/bin..."
ln -sf /opt/puppetlabs/bin/facter /usr/local/bin/facter
ln -sf /opt/puppetlabs/bin/hiera /usr/local/bin/hiera
ln -sf /opt/puppetlabs/bin/mco /usr/local/bin/mco
ln -sf /opt/puppetlabs/bin/puppet /usr/local/bin/puppet
