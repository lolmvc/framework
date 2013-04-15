# lolmvc: Simply Powerful

lolmvc, is meant to be a framework you can wrap your mind around.
It's primary purpose is to facilitate learning MVC by doing,
but could also make a nice base framework for a real app.

**Requirements: A web-server running PHP 5.4+**

*Friendly MIT Licensed*

## Quick Start

### Redhat Openshift (in development)

First, head on over to <http://openshift.redhat.com> and create
an account. Then install the client tools:
<https://openshift.redhat.com/community/developers/rhc-client-tools-install>
and follow the code below.
	
	# awesome one-liner
	rhc app create myapp diy -n mynamespace --from-code=http://github.com/lolmvc/openshift-quickstart.git

### OR Heroku (in development)

First head on over to <http://heroku.com> and create an account.
Then install the heroku toolbelt: <https://toolbelt.heroku.com>
and follow the code below.

	# awesome one-liner
	heroku create myapp --buildpack http://github.com/lolmvc/heroku-buildpack

### OR Install on your own webserver

	# go to where your webserver serves up it's sites
	cd /var/www/

	# get lolmvc
	git clone git@github.com:lolmvc/framework.git

	cd framework

	# install composer (if you don't already have it)
	curl -s https://getcomposer.org/installer | php

	# run composer to install all lolmvc dependencies
	php composer.phar install

### Then, create your app

	# make your app directory
	cp skel myapp

*For more information, please see the documentation under /doc.*

### Finally

Point your webserver to serve up the *webroot* directory under your app directory.

You can run multiple apps under the same lolmvc install, which can all consume classes from both lolmvc and your other apps.
