#!/usr/bin/env bash
# Starts a local WordPress Playground at http://127.0.0.1:9400 with this
# repo's theme and plugin mounted live (edits show up on reload).
# Playground keeps its database in memory, so scripts/blueprint.json
# installs Polylang, re-activates the theme and plugin, sets the site title
# and runs the language setup (six languages) on every start.
# Login: admin / password at /wp-login.php. Requires Node 20+.
set -euo pipefail
cd "$(dirname "$0")/.."
PORT="${PORT:-9400}"

exec npx --yes @wp-playground/cli@latest server \
  --port="$PORT" \
  --mount="$PWD/wp-content/themes/serhandemirel:/wordpress/wp-content/themes/serhandemirel" \
  --mount="$PWD/wp-content/plugins/serhandemirel-core:/wordpress/wp-content/plugins/serhandemirel-core" \
  --blueprint=scripts/blueprint.json
