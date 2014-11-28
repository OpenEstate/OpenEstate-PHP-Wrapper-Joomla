#!/bin/bash

NAME="com_openestate"
VERSION="0.4"
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rm -Rf $PROJECT_DIR/release
mkdir $PROJECT_DIR/release

cd $PROJECT_DIR/src/joomla-1.5
zip -r $PROJECT_DIR/release/$NAME-joomla15-$VERSION.zip .

cd $PROJECT_DIR/src/joomla-2.5
zip -r $PROJECT_DIR/release/$NAME-joomla25-$VERSION.zip .

cd $PROJECT_DIR/src/joomla-3.0
zip -r $PROJECT_DIR/release/$NAME-joomla30-$VERSION.zip .
