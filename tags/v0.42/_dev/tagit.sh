#!/bin/bash
echo "Tag building script for hhbdevolution project.";
if [[ "$1" != "" ]]; then
  echo "Copying trunk to /tags/v0.$1";
  svn cp https://hhbdevolution.googlecode.com/svn/trunk/ https://hhbdevolution.googlecode.com/svn/tags/v0.$1 -m "creating tag v0.$1"
else
  echo "Please specify tag number 0.xx as parameter (ie. tagit.sh \"16\")";
fi
