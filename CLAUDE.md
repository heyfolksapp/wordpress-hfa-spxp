# CLAUDE.md — Project context for Claude Code

## What this repo is

A WordPress plugin that exposes a blog via the [SPXP protocol](https://github.com/spxp/spxp-specs/blob/master/SPXP-Spec.md) (Social Profile Exchange Protocol). The plugin lives entirely in the `hfa-spxp/` subdirectory.

## Key files

| File | Purpose |
|---|---|
| `hfa-spxp/hfa-spxp.php` | Plugin entry point, registers hooks |
| `hfa-spxp/hfa-spxp-class.php` | Core logic: SPXP endpoint handlers, sanitization |
| `hfa-spxp/hfa-spxp-settings-class.php` | WP Admin settings page |
| `hfa-spxp/hfa-spxp-settings.js` | Settings page JS (image picker, nonce) |
| `hfa-spxp/readme.txt` | WordPress.org plugin directory format |

## Local test environment

Requires: `brew install php@8.2 mariadb wp-cli`

The test WordPress site lives at `~/Sites/wp-spxp-test` (user-specific, not in the repo). To set it up from scratch see README.md. To start an existing setup:

```bash
brew services start mariadb
/opt/homebrew/opt/php@8.2/bin/php -S localhost:8080 \
  -t ~/Sites/wp-spxp-test \
  ~/Sites/wp-spxp-test/router.php
```

Test the endpoints:
```bash
curl -sL http://localhost:8080/spxp | python3 -m json.tool
curl -sL http://localhost:8080/spxp/posts | python3 -m json.tool
```

The plugin is symlinked into the test site:
```bash
ln -sf $(pwd)/hfa-spxp ~/Sites/wp-spxp-test/wp-content/plugins/hfa-spxp
```

## Release process

1. Update `Version:` in `hfa-spxp/hfa-spxp.php` and `Stable tag:` in `hfa-spxp/readme.txt` to the new version
2. Add a changelog entry in `readme.txt`
3. Commit, merge to `main`
4. Tag the merge commit: `git tag v<version> && git push origin v<version>`

Note: tags use a `v` prefix (e.g. `v1.3`); the version string inside files (`hfa-spxp.php`, `readme.txt`) has no prefix (e.g. `1.3`).

Current release: **1.3** (tagged `v1.3` on `main`)

## Running tests

```bash
vendor/bin/phpunit
```

Requires `composer install` first. Composer is not system-installed on this machine; download it with:

```bash
curl -sS https://getcomposer.org/installer | /usr/local/opt/php@8.2/bin/php -- --install-dir=/tmp --filename=composer
/tmp/composer install
```

## WordPress.org plugin directory

Plugin slug: `hfa-spxp-support`
Listing: `https://wordpress.org/plugins/hfa-spxp-support/`
SVN repo: `https://plugins.svn.wordpress.org/hfa-spxp-support/` (username: `heyfolksapp`)

SVN is not system-installed; install with `brew install subversion`.

To publish a new version:
1. Follow the release process above to bump the version and tag
2. Check out the SVN repo: `svn checkout https://plugins.svn.wordpress.org/hfa-spxp-support/`
3. Copy plugin files into `trunk/`
4. `svn copy trunk/ tags/<version>/`
5. `svn commit --username heyfolksapp --message "..."`

## Open issues

_(none)_

## SPXP spec compliance

The plugin implements: `text`, `photo`, `video`, `web` post types, profile root endpoint, posts endpoint with `before`/`after`/`max` pagination.

Not yet implemented: cryptographic signing/encryption, `friendsEndpoint`, `keysEndpoint`.
