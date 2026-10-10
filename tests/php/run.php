<?php
// Runs every *_test.php in this folder.   php tests/php/run.php
require __DIR__ . '/bootstrap.php';
foreach ( glob( __DIR__ . '/*_test.php' ) as $file ) { require $file; }
exit( tmc_test_report() );
