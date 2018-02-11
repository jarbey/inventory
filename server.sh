#!/bin/sh

BASEDIR=$(dirname "$0")
echo "$BASEDIR"

export $(cat $BASEDIR/.env | grep -v ^# | xargs)

/usr/bin/php $BASEDIR/bin/console inventory:websocket:server -vv 2>&1 >> $BASEDIR/websocket.log
