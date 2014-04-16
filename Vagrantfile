# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

  config.vm.provision "puppet" do |puppet|
    puppet.manifests_path = "puppet_manifests"
    puppet.module_path = "puppet_modules"
    puppet.manifest_file = "ilios.pp"
  end

  # Forward a port from the guest to the host, if you wish to allow other people
  # access to this install then remove the host_ip parameter
  config.vm.network "forwarded_port", guest: 443, host: 8443, host_ip: "127.0.0.1"

  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :box
  end

  # Share an additional folder to the guest VM.
  config.vm.synced_folder "web", "/var/www"
  config.vm.synced_folder "web/learning_materials", "/var/www/learning_materials", :mount_options => ["uid=33,gid=33"]
  config.vm.synced_folder "web/tmp_uploads", "/var/www/tmp_uploads", :mount_options => ["uid=33,gid=33"]
  config.vm.synced_folder "web/application/logs", "/var/www/application/logs", :mount_options => ["uid=33,gid=33"]
  config.vm.synced_folder "web/application/cache", "/var/www/application/cache", :mount_options => ["uid=33,gid=33"]
end
