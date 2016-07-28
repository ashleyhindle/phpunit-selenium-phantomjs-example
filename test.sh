#!/bin/bash

if [ -d /vagrant/ ] # If we're on the VM, run the majig
then
	java -jar /usr/local/bin/selenium-server-standalone-2.53.1.jar -log /tmp/selenium.log -Dphantomjs.binary.path=/usr/local/bin/phantomjs
else
	vagrant ssh -c "/bin/bash /vagrant/test.sh"
fi
