#!/usr/bin/env php
<?php

Phar::mapPhar('srbuilder.phar');
require 'phar://srbuilder.phar/bin/srbuilder.php';

__HALT_COMPILER(); ?>