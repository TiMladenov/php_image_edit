1. Get Apache(therefore PHP too) rw permissions: 
	ps -ef | grep apache
2. Give ownership to Apache(PHP) for the upload folder:
	sudo chown www-data:www-data /upload/folder/location
	sudo chown 755 /upload/folder/location
3. Check the PHP version that the server is running:
	php -v
4. Instal GD PHP library for image manipulation:
	sudo apt-get install php*.*-gd
	(where *.* are version numbers)
5. Run sudo service apache2 restart

==================================================================
NOTES:
1. Image upload size is limited to 2MB in php.ini
