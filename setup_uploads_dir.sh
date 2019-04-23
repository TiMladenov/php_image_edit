#!/bin/sh

#Author : Tihomir Mladenov
#Date	: 23-Apr-2019

cd && cd Desktop
if [ ! -d "uploads" ] ; then
	mkdir uploads
	sudo chown www-data:www-data "uploads"
	sudo chmod 755 "uploads"
	sudo service apache2 restart
	echo "Created directory Uploads in Desktop."
else
	echo "Checking in " $(pwd) "."
	echo "Directory already exists. Nothing done."
fi
