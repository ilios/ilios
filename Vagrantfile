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

  # Boot with a GUI so you can see the screen. (Default is headless)
  # config.vm.boot_mode = :gui

  # Assign this VM to a host-only network IP, allowing you to access it
  # via the IP. Host-only networks can talk to the host machine as well as
  # any other machines on the same network, but cannot be accessed (through this
  # network interface) by any external networks.
  # config.vm.network :hostonly, "192.168.33.10"

  # Assign this VM to a bridged network, allowing you to connect directly to a
  # network using the host's network device. This makes the VM appear as another
  # physical device on your network.
  # config.vm.network :bridged

  # Forward a port from the guest to the host, which allows for outside
  # computers to access the VM, whereas host only networking does not.
  config.vm.network "forwarded_port", guest: 443, host: 8443

  # Share an additional folder to the guest VM.
  config.vm.synced_folder "web", "/var/www"
  config.vm.synced_folder "web/learning_materials", "/var/www/learning_materials", :mount_options => "uid=33,gid=33"
  config.vm.synced_folder "web/tmp_uploads", "/var/www/tmp_uploads", :mount_options => "uid=33,gid=33"
  config.vm.synced_folder "web/application/logs", "/var/www/application/logs", :mount_options => "uid=33,gid=33"
  config.vm.synced_folder "web/application/cache", "/var/www/application/cache", :mount_options => "uid=33,gid=33"
end
