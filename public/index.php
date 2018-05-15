<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Perform\PrivateProjects\SprintProgress\Application\Api;

$app = new Api();
$app->run();
