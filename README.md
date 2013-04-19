# lolmvc: Simply Powerful
lolmvc, is meant to be a framework you can wrap your mind around.
It's primary purpose is to facilitate learning MVC by doing,
but could also make a nice base framework for a real app.

## Development Status
Just shy of it's first release (1.0.0), though fully functional.

## Features
 - Super Fast
 - **PHP-FIG PSR Compliant**
 - Convention over Configuration
 - Sematic versioning
 - Implements MVC design pattern (fat model)
 - **Annotation based URL routing (phpdoc tag style)**
 - Composer use is fully integrated (Composer aware autoloader)
 - Intuitive design, intended to be easy to wrap your mind around
 - Database agnostic (although we recommend MongoDB, if you need one)
 - Run multiple apps within the same framework under different *vhosts*
 - All about choices, choose your own DB, templating engine, etc.
 - **Write your own *services* as traits or classes, shared across apps.**

## Requirements 
 - A web-server (nginx recommended)
 - PHP 5.4+

## License
Friendly MIT Licensed

## Quick Start
### Overview

#### Directory Structure

<pre>
www
 |-framework
	|- lolmvc
	|	|- model		(abstract models and model interfaces)
	|	|- view			(default views like json, etc)
	|	|- controller	(abstract controller and controller interfaces)
	|	|- service		(bundled services, aka helpers or plugins)
	|
	|- myapp			(your app name/directory correlates to your app's namespace)
	|	|- models		(model classes for your controllers)
	|	|- view			(your views)
	|	|- controllers	(your controllers)
	|	|- service		(your own services, aka helpers or plugins)
	|	|- webroot		(myapp.example.com webserver points to this directory)
	|
	|- vendor			(composer installed dependencies)
</pre>

#### How to start your app
All apps have what is called a front controller. In an lolmvc app, 
that is the index.php file in your app's webroot directory. Here is
an example index.php from myapp/webroot using the directory structure above.

	<?php
	namespace 'Myapp\Controller';

	require '../../lolmvc/app.php';
	$app = new \Lolmvc\App('myapp');

	// use app level config
	// $app->useLocalConfig();

	$app->run();

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
	# make a copy of the *skeleton* app for your app directory
	cp skel myapp

*For more information, please see the documentation under /doc.*

### Finally
Point your webserver to serve up the *webroot* directory under your app directory.

You can run multiple apps under the same lolmvc install, which can all consume classes from both lolmvc and your other apps.
