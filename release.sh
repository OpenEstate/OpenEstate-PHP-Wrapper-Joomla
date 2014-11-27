#!/bin/bash

NAME="com_openestate"
VERSION15="0.1.2"
VERSION25="0.2.2"
PROJECT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rm -Rf $PROJECT_DIR/release
mkdir $PROJECT_DIR/release

cd $PROJECT_DIR/src/joomla-1.5
zip -r $PROJECT_DIR/release/$NAME-$VERSION15.zip .

cd $PROJECT_DIR/src/joomla-2.5
zip -r $PROJECT_DIR/release/$NAME-$VERSION25.zip .
