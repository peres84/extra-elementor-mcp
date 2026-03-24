#!/bin/bash
#
# Build a distributable zip for the Extra Elementor MCP plugin.
# Usage: bash build-zip.sh
# Output: ../extra-elementor-mcp.zip (one level above the project)
#

set -e

PLUGIN_SLUG="extra-elementor-mcp"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
BUILD_DIR="$(mktemp -d)"
DEST="${BUILD_DIR}/${PLUGIN_SLUG}"
OUTPUT_DIR="$(dirname "$SCRIPT_DIR")"
OUTPUT_FILE="${OUTPUT_DIR}/${PLUGIN_SLUG}.zip"

echo "Building ${PLUGIN_SLUG}.zip ..."

# Create temp plugin folder
mkdir -p "$DEST"

# Copy only the files WordPress needs
cp "$SCRIPT_DIR/extra-elementor-mcp.php" "$DEST/"
cp "$SCRIPT_DIR/readme.txt"              "$DEST/"
cp "$SCRIPT_DIR/LICENSE"                 "$DEST/"

# Core PHP includes and assets
cp -r "$SCRIPT_DIR/includes" "$DEST/includes"
cp -r "$SCRIPT_DIR/assets"   "$DEST/assets"

echo "Packaging ..."

# Remove old zip if it exists
rm -f "$OUTPUT_FILE"

# Use PowerShell on Windows, zip on Linux/Mac
if command -v zip &> /dev/null; then
    (cd "$BUILD_DIR" && zip -r "$OUTPUT_FILE" "$PLUGIN_SLUG/")
else
    OUTPUT_WIN="$(cygpath -w "$OUTPUT_FILE" 2>/dev/null || echo "$OUTPUT_FILE")"
    DEST_WIN="$(cygpath -w "$DEST" 2>/dev/null || echo "$DEST")"
    powershell.exe -NoProfile -Command "Compress-Archive -Path '${DEST_WIN}' -DestinationPath '${OUTPUT_WIN}' -Force"
fi

# Cleanup
rm -rf "$BUILD_DIR"

echo "Done → $OUTPUT_FILE"
