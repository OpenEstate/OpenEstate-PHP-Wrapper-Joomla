#!/usr/bin/env bash
#
# Copyright 2010-2018 OpenEstate.org
#

NAME="com_openestate"
VERSION="0.5-SNAPSHOT"
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rm -Rf "$DIR/release"
mkdir "$DIR/release"

cd "$DIR/src"
zip -r "$DIR/release/$NAME-$VERSION.zip" .
