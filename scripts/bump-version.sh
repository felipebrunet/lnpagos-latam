#!/usr/bin/env bash

if [ -z "$1" ]; then
	echo "Missing new version argument"
	exit 1
else
	new_version=$1
fi

sed -i -E "s/Version: [0-9]\.[0-9]\.[0-9]/Version: $new_version/" lnpagos.php 
sed -i -E "s/Stable tag: [0-9]\.[0-9]\.[0-9]/Stable tag: $new_version/" readme.txt

git add .
# git add lnpagos.php
# git add readme.txt
git commit -m "Bump version to $new_version"
git tag "v$new_version"

git push origin --tags
