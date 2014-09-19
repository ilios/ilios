# -*- mode: ruby -*-
# vi: set ft=ruby :
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
    config.ssh.shell = "bash -c 'BASH_ENV=/home/vagrant/.bashrc exec bash'"
    config.vm.box = "puppetlabs/ubuntu-14.04-64-puppet"
    config.vm.box_url = "https://vagrantcloud.com/puppetlabs/ubuntu-14.04-64-puppet/version/3/provider/virtualbox.box"
    config.vm.hostname = "ilios.dev"
    config.vm.network :private_network, ip: "10.10.10.10"
    config.vm.network "forwarded_port", guest: 443, host: 8443, host_ip: "127.0.0.1"
    config.vm.network "forwarded_port", guest: 3306, host: 13306, host_ip: "127.0.0.1"
    config.vm.synced_folder ".", "/vagrant", :nfs => { :mount_options => ["dmode=777","fmode=777"], :nfs_version => "4" }, id: "vagrant-root"

    config.vm.provider "virtualbox" do |vb|
        vb.customize ["modifyvm", :id, "--memory", "1024"]
        vb.name = "ilios.dev"
    end

    config.vm.provision "shell" do |shell|
        shell.path = "provision/shell/init.sh"
    end

    if Vagrant.has_plugin?("vagrant-cachier")
        config.cache.scope = :box
        config.cache.synced_folder_opts = {
            type: :nfs
        }
    end
end
