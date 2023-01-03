#!/usr/bin/env bash

PHP_COMMAND=php

$PHP_COMMAND artisan command:read_feed
$PHP_COMMAND artisan command:dispatch_post
$PHP_COMMAND artisan queue:work --once
