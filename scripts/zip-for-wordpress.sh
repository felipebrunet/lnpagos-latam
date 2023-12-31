#!/usr/bin/env bash

latest_tag=$(git describe --tags --abbrev=0)
zip -r lnpagos-latam-${latest_tag}.zip . -x '.git/*' -x 'bin/*' -x 'tests/*' -x 'phpunit.xml.dist' -x '.*' -x '*.zip'
