#!/usr/bin/env bash

svn_dir=wordpress-svn/
svn_trunk_dir=${svn_dir}trunk/
svn_assets_dir=${svn_dir}assets/
latest_tag=$(git describe --tags --abbrev=0)


if [ ! -d $svn_dir ]; then
	echo "Creating svn directories at ${svn_dir}"
	mkdir $svn_dir
	svn co https://plugins.svn.wordpress.org/lightning-payment-gateway-lnbits $svn_dir
fi

echo "Copying files..."
cp -r includes $svn_trunk_dir
cp -r templates $svn_trunk_dir
cp -r assets $svn_trunk_dir
cp readme.txt $svn_trunk_dir
cp lnbits.php $svn_trunk_dir
mv $svn_trunk_dir/assets/icon-* $svn_assets_dir

cd $svn_dir
# svn add *
echo "Review changes:"
svn diff

read -p "Are you sure you want to commit to WordPress directory? " -n 1 -r
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
	exit 0
fi


echo "Committing to svn repo"
echo "Please type in release message for ${latest_tag}"
read release_msg

svn ci -m "${release_msg}" --username phaedrus1984
