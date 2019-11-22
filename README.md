# Image editor

This is a simple web app where the user could upload and edit an image.

# Technology stack
- **Frontend**:
<img src="https://img.stackshare.io/service/2538/kEpgHiC9.png" width="25" height="25"> <img src="https://img.stackshare.io/service/6727/css.png" width="25" height="25"> <img src="https://img.stackshare.io/service/1101/C9QJ7V3X.png" width="25" height="25"> <img src="https://img.stackshare.io/service/1209/javascript.jpeg" width="25" height="25">

- **Backend**: <img src="https://img.stackshare.io/service/991/php.png" width="25" height="25">

# Application features

In this web app the users could select an image, change its size, add text to it with a certain size. If the text is longer than the width of the image, it will be wrapped on a new line. The original image and the edited image will be saved locally on the server with the edited image
being returned to the frontend for the user to download.
It is somewhat similar to a meme generator.

# Prerequisites to run this application

PHP 7.0+ with the respective packages installed to handle Apache 2 web server communication. The following packaged are needed:

```
$ sudo apt-get update
$ sudo apt-get install apache2 php7.0 libapache2-mod-php7.0 php-7.0gd
$ sudo service apache2 restart
```

A folder needs to be created for the images to be uploaded to the server:

```
$ ps -ef | grep apache
$ mkdir /home/ubuntu/Desktop/uploads/
$ sudo chown www-data:www-data /home/ubuntu/Desktop/uploads/
$ sudo chown 755 /home/ubuntu/Desktop/uploads/
$ sudo service apache2 restart
```


# Web app address
http://edit.tmladenov.tech/
__Current max upload size is limited to 2 MB. JPG and PNG are supported.__

# License
BSD License
>Copyright (c) 2019, Tihomir Mladenov, tihomir.mladenov777@gmail.com
All rights reserved.

>Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

>1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.
>2. Redistributions in binary form must reproduce the above copyright notice,
   this list of conditions and the following disclaimer in the documentation
   and/or other materials provided with the distribution.

>THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

>The views and conclusions contained in the software and documentation are those
of the authors and should not be interpreted as representing official policies,
either expressed or implied, of the Image Editor project.
