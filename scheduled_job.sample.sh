#!/usr/bin/env bash

PHP_COMMAND=php

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
cd $SCRIPT_DIR
$PHP_COMMAND artisan command:read_feed
$PHP_COMMAND artisan command:dispatch_post
$PHP_COMMAND artisan queue:work --once
