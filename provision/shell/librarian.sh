#!/bin/sh
PUPPET_DIR=/etc/puppetlabs/code

echo "Installing gem requirements..."
apt-get update -qq && apt-get install -y -q ruby1.9.1-dev make augeas-tools libaugeas-ruby libaugeas-ruby1.9.1 libaugeas-dev
ln -sf /usr/include/libxml2/libxml /usr/include/libxml ##TMP FIX (Bug in augeas gem)
gem install rgen augeas

echo "Installing GIT for Librarian"
$(which git > /dev/null 2>&1)
FOUND_GIT=$?
if [ "$FOUND_GIT" -ne '0' ]; then
  apt-get -q -y install git-core
fi

if [ ! -d "$PUPPET_DIR" ]; then
  mkdir -p "$PUPPET_DIR"
fi

cp /vagrant/Puppetfile $PUPPET_DIR

if [ "$(gem list -i '^librarian-puppet$')" = "false" ]; then
  gem install librarian-puppet --no-ri --no-rdoc
  cd $PUPPET_DIR && librarian-puppet install --clean --verbose
else
  cd $PUPPET_DIR && librarian-puppet update
fi
