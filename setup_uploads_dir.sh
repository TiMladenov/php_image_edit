#!/bin/sh

#Author : Tihomir Mladenov
#Date	: 23-Apr-2019

cd && cd Desktop
if [ ! -d "uploads" ] ; then
	mkdir uploads
	echo "Created directory Uploads in Desktop."
else
	echo "Checking in " $(pwd) "."
	echo "Directory already exists. Nothing done."
fi
