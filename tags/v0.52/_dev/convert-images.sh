#!/bin/bash

find . -name "*.jpg" | while read f;
do
  echo $f
  convert $f -thumbnail 67x67 -strip -format jpg -quality 65  JPG:./th/${f%.*}-th.jpg
  # convert $f -thumbnail 50x50 -strip -format jpg -quality 5  JPG:../albums50/${f%.*}-th.jpg
  # convert $f -resize 300x300 -strip JPG:../albums-logo/$f
done