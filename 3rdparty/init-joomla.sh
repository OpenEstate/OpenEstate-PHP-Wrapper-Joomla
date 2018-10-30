#!/usr/bin/env bash
#
# Copyright 2010-2018 OpenEstate.org
#

URL="https://downloads.joomla.org/cms/joomla3/3-8-13/Joomla_3-8-13-Stable-Full_Package.tar.gz?format=gz"

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TEMP_DIR="$DIR/temp"
JOOMLA_DIR="$DIR/joomla"
set -e

echo ""
echo "Downloading latest version of Joomla..."
mkdir -p "$TEMP_DIR"
rm -Rf "$TEMP_DIR/joomla.tar.gz"
curl -L \
  -o "$TEMP_DIR/joomla.tar.gz" \
  "$URL"
if [ ! -f "$TEMP_DIR/joomla.tar.gz" ]; then
    echo "ERROR: Joomla was not properly downloaded!"
    exit 1
fi

echo ""
echo "Extracting Joomla..."
rm -Rf "$JOOMLA_DIR"
rm -Rf "$TEMP_DIR/joomla"
mkdir -p "$TEMP_DIR/joomla"
cd "$TEMP_DIR/joomla"
tar xfz "$TEMP_DIR/joomla.tar.gz"
#mv "$(ls -1)" "$JOOMLA_DIR"
#rm -Rf "$TEMP_DIR/wordpress"
mv "$TEMP_DIR/joomla" "$JOOMLA_DIR"

echo ""
echo "Copying default configuration..."
cp "$JOOMLA_DIR/installation/configuration.php-dist" "$JOOMLA_DIR/configuration.php"

echo ""
echo "Joomla was successfully extracted!"
echo "to: $JOOMLA_DIR"
