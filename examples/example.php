<?php

require __DIR__ . '/../vendor/autoload.php';

$client = new \NarodmonApi\Client('uuid', 'api_key');

var_dump($client->login('login', 'passwd'));

var_dump($client->sensorInit());

$devices = $client->publicSensors(50, [1, 2, 3]);

foreach($devices['devices'] as $device) {
    echo $device['id'], "\t", $device['name'], PHP_EOL;
    foreach ($device['sensors'] as $sensor) {
        echo "\t" . $sensor['id'], "\t", $sensor['name'], "\t", $sensor['value'], ' ', $sensor['unit'], PHP_EOL;
    }
    echo PHP_EOL;
}

var_dump($client->logout());
