#!/bin/bash

set -euo pipefail

# Configuration
URL="https://raw.githubusercontent.com/jshttp/mime-db/master/db.json"
FILE="db.json"
DESTINATION="resources/mime-db"

# Ensure destination directory exists
mkdir -p "$DESTINATION"

# Download the file
if curl -fsSL "$URL" -o "$DESTINATION/$FILE"; then
  exit 0
else
  exit 1
fi
