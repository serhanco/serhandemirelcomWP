#!/usr/bin/env bash
# Starts a local WordPress Playground at http://127.0.0.1:9400 with this
# repo's theme mounted live (edits show up on reload). The theme needs no
# plugin; Playground keeps its database in memory, so scripts/blueprint.json
# installs Polylang (for the six languages), re-activates the theme, sets
# the site title and runs the language setup on every start.
# Login: admin / password at /wp-login.php. Requires Node 20+.
set -euo pipefail
cd "$(dirname "$0")/.."
PORT="${PORT:-9400}"

exec npx --yes @wp-playground/cli@latest server \
  --port="$PORT" \
  --mount="$PWD/wp-content/themes/serhandemirel:/wordpress/wp-content/themes/serhandemirel" \
  --blueprint=scripts/blueprint.json
