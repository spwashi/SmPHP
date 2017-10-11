#!/bin/bash


if [ "$#" -ne 1 ]
then
  echo "Should have supplied a path relative to the SmPHP installation root."
  exit 1
fi

echo "creating..."
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
site_path=$1
site_path="$DIR/../$site_path"

echo "in $site_path"



###############################################
# Creating folders
###############################################
# Config Dir
mkdir -p ${site_path}/app/config/
mkdir -p ${site_path}/app/config/autoload/dev
mkdir -p ${site_path}/app/config/routes/dev
mkdir -p ${site_path}/app/config/models/dev
mkdir -p ${site_path}/app/config/sources/dev

# Controllers (business logic)
mkdir -p ${site_path}/app/src/Controller

# Template Dirs
mkdir -p ${site_path}/app/view/twig
mkdir -p ${site_path}/app/view/php
mkdir -p ${site_path}/app/view/html
mkdir -p ${site_path}/app/view/react

# Compiled CSS & JS
mkdir -p ${site_path}/public/css
mkdir -p ${site_path}/public/js

# Helper Scripts
mkdir -p ${site_path}/scripts/config

# Test Directory
mkdir -p ${site_path}/test
mkdir -p ${site_path}/test/app/src/Controller

# Vendor folder
mkdir -p ${site_path}/vendor
#
mkdir -p ${site_path}/storage/app/
mkdir -p ${site_path}/storage/app/cache
mkdir -p ${site_path}/storage/app/compiled



###############################################
# Creating files
###############################################
cp -r ./site/. ${site_path}