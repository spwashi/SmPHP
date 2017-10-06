#!/bin/bash

echo "What would you like to name this site?"

read SITE_DIR_NAME



mkdir -p ../sites/${SITE_DIR_NAME}/{app/{config/{autoload,routes/dev,models/dev,sources/dev},view/{react,twig}},public/{js,css},scripts/config,src/Controller,test,storage/app/{cache,compiled}}

touch ../sites/${SITE_DIR_NAME}/{app/{config/{autoload/autoload.php,models/models.json,routes/routes.json,sources/sources.json autoload/autoload.php,models/models.json,routes/routes.php,sources/sources.json},src/{Controller/HomeController.php}}}