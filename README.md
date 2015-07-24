[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/badges/build.png?b=master)](https://scrutinizer-ci.com/g/kuzmichus/NarodmonRuApi/build-status/master)


# NarodmonRuApi


Клиент для API сайте http://narodmon.ru/.

Использование:

```php

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

```
