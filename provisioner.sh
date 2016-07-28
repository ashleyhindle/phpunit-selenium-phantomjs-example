#!/bin/bash
export FILE="phantomjs-2.1.1-linux-x86_64.tar.bz2"
export SELENIUMFILE="selenium-server-standalone-2.53.1.jar"

touch /tmp/selenium.log
chmod a+wr /tmp/selenium.log

mkdir -p /tmp/provisioner/
cd /tmp/provisioner/

apt-get update

### Selenium
apt-get -y install openjdk-7-jre-headless
curl -sL -o $SELENIUMFILE "http://selenium-release.storage.googleapis.com/2.53/${SELENIUMFILE}"
# No checksums provided to verify?

### PhantomJS

if [ -f /vagrant/$FILE ]
then
	cp /vagrant/$FILE ./
else
	curl -sL -o $FILE "https://bitbucket.org/ariya/phantomjs/downloads/${FILE}"

	hash=$(sha256sum $FILE | cut -d' ' -f1)
	if [ $hash != "86dd9a4bf4aee45f1a84c9f61cf1947c1d6dce9b9e8d2a907105da7852460d2f" ]
	then
		echo "Hash is wrong, can't continue.. [${hash}]"
		exit 1
	fi
fi

bzip2 -d $FILE

NEWFILE=${FILE::-4} # Take off .bz2

echo $NEWFILE
tar -xvf $NEWFILE
NEWFILE=${NEWFILE::-4} # Take off .tar


### Move to /usr/local/bin/
mv $NEWFILE/bin/phantomjs /usr/local/bin/
mv $SELENIUMFILE /usr/local/bin
chmod a+x /usr/local/bin/phantomjs
chmod a+x /usr/local/bin/$SELENIUMFILE
