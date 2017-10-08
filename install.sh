#!/bin/bash

echo "What would you like to name this site?"


read new_dir

cd ./scripts/ && ./structure.sh ${new_dir}