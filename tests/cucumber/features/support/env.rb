require 'rspec/expectations'
require 'capybara'
require 'test/unit/assertions'
require 'vagrant'

World(Test::Unit::Assertions)

Capybara.ignore_hidden_elements = true

# Some AJAX calls are slow. :-( 
# Increase timeout from 2s to 5s until we get around to improving performance.
Capybara.default_wait_time = 5;

Before do 
    env = Vagrant::Environment.new

    # make sure our test environment is up and running
    if !env.primary_vm.created? or env.primary_vm.state == :poweroff
        env.cli('up')
    end 
    if env.primary_vm.state != :running
        env.cli('resume')
    end

    env.primary_vm.channel.execute('cd /vagrant/puppet_manifests && /usr/bin/sudo /opt/vagrant_ruby/bin/puppet apply reset_db.pp')
    Capybara.app_host = "https://localhost:8443"
end

World do
    Capybara::Session.new(:selenium)
end
