<?php
// Prevent WordPress plugin files from calling exit when loaded outside WP
define( 'ABSPATH', __DIR__ . '/' );

require_once __DIR__ . '/../vendor/autoload.php';

// Load only the class files — not the entry point, which instantiates and
// registers hooks that would require a full WordPress environment.
require_once __DIR__ . '/../hfa-spxp/hfa-spxp-class.php';
require_once __DIR__ . '/../hfa-spxp/hfa-spxp-settings-class.php';
