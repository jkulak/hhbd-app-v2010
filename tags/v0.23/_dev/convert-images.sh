#!/bin/bash


cd ./albums

find . -name "*.jpg" | while read f;
do

echo $f
# convert $f -thumbnail 67x67 -strip -format png -quality 5  PNG8:../albums67/${f%.*}.png
# convert $f -thumbnail 50x50 -strip -format png -quality 5  PNG8:../albums50/${f%.*}.png

convert $f -resize 300x300 -strip JPG:../albums-logo/$f

done