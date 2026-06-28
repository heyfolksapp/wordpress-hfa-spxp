# Wordpress Plugin to expose your blog via the SPXP protocol

Exposes your blog via the [Social Profile Exchange Protocol](https://github.com/spxp/spxp-specs) so your friends can
follow your updates using any SPXP client of their choice, like the [HeyFolks app](https://heyfolks.app).

## Installation
As usual: Copy the `/hfa-spxp` folder to `/wp-content/plugins` of your wordpress
instance, activate the plugin and then adjust it to your liking on the SPXP
settings page.

## Description
The Social Profile Exchange Protocol (SPXP) defines how independent clients and
servers can exchange information about social profiles, focusing on privacy,
security and individual sovereignty.  
It aims to create a social media network consisting of independent actors rather
than being controlled by a single entity.

This plugin makes your wordpress instance available via this protocol so that
people can start following your blog posts with any SPXP client of their liking,
like the [Hey Folks app](https://HeyFolks.app/).

You can set the profile picture and a longer "About" text in the settings and
tweak how posts on our Blog are presented via SPXP.

This plugin is not yet available for multisite installations.

## Development

### Local environment setup

Requires Homebrew on macOS:

```bash
brew install php@8.2 mariadb wp-cli
brew services start mariadb

# Create database
mariadb -u $(whoami) -e "CREATE DATABASE IF NOT EXISTS wp_spxp_test; \
  CREATE USER IF NOT EXISTS 'wp'@'localhost' IDENTIFIED BY 'wp'; \
  GRANT ALL ON wp_spxp_test.* TO 'wp'@'localhost';"

# Download and install WordPress
mkdir -p ~/Sites/wp-spxp-test
wp core download --path=~/Sites/wp-spxp-test
wp config create --dbname=wp_spxp_test --dbuser=wp --dbpass=wp \
  --path=~/Sites/wp-spxp-test --skip-check
wp core install --url=http://localhost:8080 --title="SPXP Test" \
  --admin_user=admin --admin_password=admin \
  --admin_email=test@example.com --path=~/Sites/wp-spxp-test
wp rewrite structure '/%postname%/' --path=~/Sites/wp-spxp-test

# Symlink plugin and activate
ln -sf $(pwd)/hfa-spxp ~/Sites/wp-spxp-test/wp-content/plugins/hfa-spxp
wp plugin activate hfa-spxp --path=~/Sites/wp-spxp-test
```

### Running the test site

```bash
brew services start mariadb
/opt/homebrew/opt/php@8.2/bin/php -S localhost:8080 \
  -t ~/Sites/wp-spxp-test \
  ~/Sites/wp-spxp-test/router.php
```

Test the SPXP endpoints at `http://localhost:8080/spxp` and `http://localhost:8080/spxp/posts`.

### Releasing

1. Update `Version:` in `hfa-spxp/hfa-spxp.php` and `Stable tag:` in `hfa-spxp/readme.txt`
2. Add a changelog entry in `readme.txt`
3. Commit and merge to `main`
4. Tag the merge commit: `git tag <version> && git push origin <version>`
