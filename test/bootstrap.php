<?php

use DevTheorem\Phaster\Test\src\{App, Config, LocalConfig};

require 'vendor/autoload.php';

if (class_exists(LocalConfig::class)) {
    // suppress error when LocalConfig doesn't exist
    /** @psalm-suppress MixedAssignment */
    App::$config = new LocalConfig();
} else {
    App::$config = new Config();
}
