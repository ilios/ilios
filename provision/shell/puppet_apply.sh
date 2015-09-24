#!/bin/sh

echo "Apply puppet..."
puppet apply --environment dev --hiera_config /vagrant/provision/puppet/hiera.yaml /vagrant/provision/puppet/environments/dev/manifests
