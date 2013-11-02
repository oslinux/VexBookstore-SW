# VexBookstore setup guide

Welcome in VexBookstore, the most alternative Bookstore manager in the world.
In the next few minutes (Or hours, depending on your experience) you will be guided to setup the bookstore on your servers (Or local computer).

### 0. Requirements
You will need at least a(1) unix server to run the different modules, **Backend**, **Frontend** and **Widget**.

If you use a single server, those are the minimal software requirements:

 * MySQL >= 5.6 - Needed for fulltext searches in InnoDB tables.
 * Tomcat 7
 * Apache2
 * PHP >= 5.5
 * PHP **intl extension** - Needed for Symfony-2.3
 * PHP **pecl_http extension** - Needed for Widget
 * Syslog - Only if you need log messages from Backend.
 * git - To download sources and Frontend.

### 1. Download Backend and Database

We've packaged a default database and precompiled WAR, you will find it [on VexDev.com](http://www.vexdev.com/bookstore/bookstore-be.tar.gz) but we're on a unix guide, so let's download it and extract from bash:

```bash
wget http://www.vexdev.com/bookstore/bookstore-be.tar.gz
tar xvzf bookstore-be.tar.gz
```

If you want, you can verify MD5 Hash of downloaded file before extract it:

```bash
md5sum bookstore-be.tar.gz
```

Original hash is **503b59fcfac07707b3ef30beea028803**

### 2. Create database, import tables and create default admin

We must create a database for our bookstore, while you can easily customize database name, user and password, we will utilize VexBookstore's defaults which are:

 * Database Name: **pwtest**
 * Username: **pwuser**
 * Password: **pwpassword**

Now, we will assume that you already have configured your mysql database to have a root user, you will need to import the default tables from our create.sql, this will also create default database, username and password (Grants access from localhost only)

```bash
mysql -u{username} -p{password} < create.sql
```

Lastly you should create a default admin to access the backend, so login to mysql:
```bash
mysql -u{username} -p{password}
```

And then you can execute the insert query on our database (We're using a sample user):

```sql
USE pwtest;
INSERT INTO admin (email, password, display_name) VALUES ("info@vexdev.com", "vexdev", "VexDev");
exit
```

### 3. Deploy Backend to tomcat

Find out what is your tomcat's appBase, ours was **/usr/share/tomcat-7/webapps**, deploying an application with tomcat (Default) autoDeploy should be as simple as copying it's WAR inside appBase, so we'll do:

```bash
cp ROOT.war /usr/share/tomcat-7/webapps/
```

The backend will be automatically extracted to ROOT (Make sure you did not have another application already installed in ROOT or change WAR's name to deploy it in another directory).

### 4. Test your backend!

Assuming you deployed in the ROOT context of your localhost (And did not change tomcat's default ports) you will be now able to access the backend at this URL:

 **http://localhost:8080**

And try logging in using your Admin user, ALL DONE! Let's switch to the FrontEnd.

### 5. Download Frontend

So, the frontend. As you already know, we're using github to publish our work and you can get the frontend straight from git.

First you will have to create and configure a directory for web access with Apache, as we're using Symfony, you will have to customize your webhost to point to web/ subdirectory of your htdocs:

```apache
<VirtualHost *:80>
    ServerAdmin luca@vexdev.com
    DocumentRoot "/var/www/localhost/htdocs/web/"
    ServerName vexstore.local
    ErrorLog "logs/vexstore"
</VirtualHost>
```

Now, go to htdocs and execute this:

```bash
git clone git://github.com/oslinux/VexBookstore-FE.git ./
```

And you're done downloading the frontend.

### 6. Install Frontend

There are just a few steps before you can use the frontend, first one you should make sure that everything under /htdocs is accessible to apache's user/group, best way to do this is chowning everything to apache:apache, so from htdocs/ do this:

```bash
chown -R 777 apache:apache *
```

Next you should install VexStore dependencies:

```bash
php composer.phar install --optimize-autoloader
```

And clean caches:

```bash
php app/console cache:clear --env=prod --no-debug
```

And you are DONE installing Frontend.

### 7. Installing Search Widget in remote site

So you decided to opt for a cross-domain Search Widget, setting up the sample project is really easy, go to the htdocs root directory of the domain in which you want to install the search widget and execute following command:

```bash
git clone git://github.com/oslinux/VexBookstore-SW.git ./
```

And you're ready.

If you need just the widget, you could add the needed files:

```bash
vbsw.css
vbsw.js
vbsw.php
simple_html_dom.php
```

load CSS and Javascript in the head your web page:

```html
<link href="./vbsw.css" rel="stylesheet">
<script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="vbsw.js" type="text/javascript"></script>
```

do not forget jQuery, now you can simply assign the class **vex-vbsw** to the element where you want the search widget to appear, like this:

```html
<div id="vex-vbsw"></div>
```

### 8. That's all, folks!

Don't forget to see youtube user's guide! <link to arrive>