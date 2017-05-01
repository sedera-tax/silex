<?php

$job = $app['controllers_factory'];
$job->get('/', function () {
    return 'Job home page'; 
});

return $job;