# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.box = "precise32"
  config.vm.box_url = "http://files.vagrantup.com/precise32.box"

  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "puppet_manifests"
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
  config.vm.forward_port 443, 8443

  # Share an additional folder to the guest VM. The first argument is
  # an identifier, the second is the path on the guest to mount the
  # folder, and the third is the path on the host to the actual folder.
  # config.vm.share_folder "v-data", "/vagrant_data", "../data"
  config.vm.share_folder "docroot", "/var/www", "web"
  config.vm.share_folder "learning_materials", "/var/www/learning_materials", "web/learning_materials", :extra => "uid=33,gid=33"
  config.vm.share_folder "tmp_uploads", "/var/www/tmp_uploads", "web/tmp_uploads", :extra => "uid=33,gid=33"
  config.vm.share_folder "application/logs", "/var/www/application/logs", "web/application/logs", :extra => "uid=33,gid=33"
  config.vm.share_folder "application/cache", "/var/www/application/cache", "web/application/cache", :extra => "uid=33,gid=33"
end
