# Installation d'un environnement de développement

```bash
sudo apt-get install virtualbox resolvconf dnsmasq
wget https://dl.bintray.com/mitchellh/vagrant/vagrant_1.9.6_x86_64.deb
sudo dpkg -i vagrant_1.9.6_x86_64.deb
vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-share
vagrant plugin install landrush
vagrant up

sudo sh -c 'echo "server=/vagrant.dev/127.0.0.1#10053" > /etc/dnsmasq.d/vagrant-landrush'
sudo service dnsmasq restart
```

# Déploiement des sources

```bash
# Test
ant deploy -Dprofile=pastishosting

# Déploiement effectif
ant deploy -Dprofile=pastishosting -Drsync.options=--delete-after
```
