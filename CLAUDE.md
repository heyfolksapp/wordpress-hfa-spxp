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
4. Tag the merge commit: `git tag <version> && git push origin <version>`

Current release: **1.2** (tagged `1.2` on `main`)

## Open issues

- **#2** — Add SPXP `video` post type support (implementation plan in the issue)
- **#3** — Publish plugin to the WordPress.org plugin directory

## SPXP spec compliance

The plugin implements: `text`, `photo`, `web` post types, profile root endpoint, posts endpoint with `before`/`after`/`max` pagination.

Not yet implemented: `video` post type (issue #2), cryptographic signing/encryption, `friendsEndpoint`, `keysEndpoint`.
