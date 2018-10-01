# ccrp_soils_wp
The WordPress Site for the Soils Health Toolkit / Kobotoolbox link app.

## Structure / Tech setup
The main wordpress site, along with some plugins and themes, is all installed and maintained via Composer - thanks to (johnpbloch)[https://github.com/johnpbloch] for their composer-enabled [WordPress repository](https://github.com/johnpbloch/wordpress).

The site uses the excellent (DataTables)[datatables.net] jQuery plugin to present data from the custom mySQL schema that holds the users' soils data. We also use the propriatory Editor addon to Datatables to handle write operations to the database. Both Datatables and the Editor are bundled into a custom WordPress plugin, which is installed via Composer from the Stats4SD BitBucket account. The Datatables plugin is stored on BitBucket as we need to keep it private given the propriatory code in use. 

**todo** - setup a public version of the plugin, with instructions on where to put the Editor files, so users with a licence can take a copy.

This site also has a custom plugin (ccrp-soils) and theme (currently a child of the (Bootstrap Starter)[https://en-gb.wordpress.org/themes/wp-bootstrap-starter/], but likely to change soon). These custom plugins are included in this repository.

## Cloning for local development
To setup the dependancies locally, you need to have both Composer and Yarn installed on your computer.


## Cloning onto a server

Installing the dependancies requires composer and yarn. Make sure you have them both installed [composer instructions](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos); for yarn, run `npm install -g yarn` (though there's discussion about whether installing via npm is [good practice or not...](https://stackoverflow.com/questions/40025890/why-wouldnt-i-use-npm-to-install-yarn).

Once done, install this repo:

1. `git clone` into your webroot folder.
2. `cd` into your new repo folder and run `composer install`.
3. 

## Updating
