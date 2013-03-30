#!/bin/sh -e
cd $(dirname $0)/..

rsync -ru --delete --exclude=".*" --exclude="*.mp3" --exclude="config/Config.php" --exclude="logs" --exclude="dev" --progress . denkmal@denkmal.org:~
