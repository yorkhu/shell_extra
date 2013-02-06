#! /bin/bash

WWW_DIR="/Applications/MAMP/htdocs";
PROJECT_DIR="$HOME/projects";

if [ -z $1 ] ; then
  echo "Usage: $0 <project name> [project group]";
  exit 1;
fi

PROJECT=$1;

if [ -z $2 ] ; then
  GROUP="";
else
  GROUP=$2;
fi

if [[ ! -d $PROJECT_DIR/$GROUP/$PROJECT ]] ; then
  mkdir -p $PROJECT_DIR/$GROUP/$PROJECT;
  echo "Created project directory: $PROJECT_DIR/$GROUP/$PROJECT";
  echo "CREATE DATABASE $1;"|mysql -u drupal -pdrupal" 
else
  echo "$PROJECT project directory already exists!";
fi
  
if [[ ! -d "$WWW_DIR/$PROJECT" ]] ; then
  ln -s $PROJECT_DIR/$GROUP/$PROJECT $WWW_DIR/$PROJECT;
  echo "Created web directory: $WWW_DIR/$PROJECT";
else
  echo "$PROJECT web directory already exists!";
fi
  
