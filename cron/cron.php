#!/usr/bin/php
<?php
/*
|--------------------------------------------------------------
| CRON JOB BOOTSTRAPPER
|--------------------------------------------------------------

This section is used to get a cron job going, using standard
CodeIgniter controllers and functions.

1) Set the path of your ilios web root - the directory that contains the ilios.php file.
2) Make this file executable (chmod a+x cron.php)
3) You can then use this file to call any controller function:

   ./cron.php --webroot=/path/to/ilios/webroot --run=/controller/method [--show-output] [--log-file=logfile] [--time-limit=N]

GOTCHA:
Do not load any authentication or session libraries in
controllers you want to run via cron. If you do, they probably
won't run right.

*/

// only allow cmd line execution of this script
if ('cli' !== php_sapi_name()) {
    exit(1);
}

// bump up the max. allowed memory limit to 256 MB
// see Redmine ticket #2960
ini_set('memory_limit','256M');

// Test for this in your controllers if you only want them accessible via cron
define('CRON', true);

# Parse the command line
$script = array_shift($argv);
$cmdline = implode(' ', $argv);
$usage = "Usage: cron.php --webroot=/path/to/ilios/webroot --run=/controller/method [--show-output][-S] [--log-file=logfile] [--time-limit=N]\n\n";
$required = array(
    '--run' => FALSE,
    '--webroot' => FALSE
);
foreach($argv as $arg) {
    $splitArg = explode('=', $arg);
    switch($splitArg[0]) {
        case '--run':
            // Simulate an HTTP request
            $_SERVER['PATH_INFO'] = $splitArg[1];
            $_SERVER['REQUEST_URI'] = $splitArg[1];
            $_SERVER['SERVER_NAME'] = 'localhost'; // does not matter
            $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
            $required['--run'] = TRUE;
            break;
        case '-S':
        case '--show-output':
            define('CRON_FLUSH_BUFFERS', TRUE);
            break;
        case '--log-file':
            if(is_writable($splitArg[1])) {
                define('CRON_LOG', $splitArg[1]);
            } else {
                die("Logfile {$splitArg[1]} does not exist or is not writable!\n\n");
            }
            break;
        case '--time-limit':
            define('CRON_TIME_LIMIT', $splitArg[1]);
            break;
        case '--webroot':
            define('CRON_CI_INDEX', $splitArg[1] . '/ilios.php');   // Your CodeIgniter main file
            $required['--webroot'] = TRUE;
            break;
        default:
            die($usage);
    }
}

if(! defined('CRON_LOG')) {
    define('CRON_LOG', 'cron.log');
}
if(! defined('CRON_TIME_LIMIT')) {
    define('CRON_TIME_LIMIT', 0);
}
// check if all mandatory arguments have been provided
foreach ($required as $arg => $present) {
    if(! $present) {
        die($usage);
    }
}



# Set run time limit
set_time_limit(CRON_TIME_LIMIT);


# Run CI and capture the output
ob_start();

chdir(dirname(CRON_CI_INDEX));
require(CRON_CI_INDEX);
$output = ob_get_contents();

if (defined('CRON_FLUSH_BUFFERS') && CRON_FLUSH_BUFFERS) {
    try {
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
    } catch( Exception $e ) {
        // @todo
    }
} else {
    ob_end_clean();
}

# Log the results of this run
error_log("### " . date('Y-m-d H:i:s') . " cron.php ". $cmdline . PHP_EOL, 3, CRON_LOG);
error_log(str_replace("\n", "\r\n", $output), 3, CRON_LOG);
error_log("\r\n### \r\n\r\n", 3, CRON_LOG);

echo PHP_EOL . PHP_EOL;
