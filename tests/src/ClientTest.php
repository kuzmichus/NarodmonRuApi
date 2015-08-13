<?php


namespace NarodmonApi\Tests;

use NarodmonApi\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSensorInit()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorInit'),
                $this->equalTo(['version'   => '1.1', 'platform'  => '6.0'])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorInit());
    }

    public function testGetLocation()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('getLocation')
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->getLocation());
    }

    public function testSetLocation()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('setLocation'),
                $this->equalTo(['lat'   => 1, 'lng'   => 2])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->setLocation(1, 2));
    }

    public function testMySensors()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorNear'),
                $this->equalTo(['my' => 1, 'radius' => 10000, 'types' => []])
            )
            ->willReturn(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $client->mySensors());

        unset($client);

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorNear'),
                $this->equalTo(['my' => 1, 'radius' => 10000, 'types' => [1, 2, 3]])
            )
            ->willReturn(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $client->mySensors([1, 2, 3]));
    }

    public function testPublicSensors()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorNear'),
                $this->equalTo(['pub' => 1, 'radius' => 100, 'types' => []])
            )
            ->willReturn(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $client->publicSensors());

        unset($client);

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorNear'),
                $this->equalTo(['pub' => 1, 'radius' => 200, 'types' => [1, 2, 3], 'lat'   => 1, 'lng'   => 2])
            )
            ->willReturn(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $client->publicSensors([1, 2, 3], 200, 1, 2));
    }

    public function testSensorNear()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorNear'),
                $this->equalTo(['my' => 0, 'pub' => 0, 'radius' => 200, 'types' => [1, 2], 'lat'   => 1, 'lng'   => 2])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorNear(false, false, [1, 2], 200, 1, 2));
    }

    public function testSensorDev()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorDev'),
                $this->equalTo(['id'   => 1])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorDev(1));
    }

    public function testGetSensorFav()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorFav')
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorFav());
    }

    public function testSetSensorFav()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorFav'),
                $this->equalTo(['sensors' => [1, 2, 3]])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorFav([1, 2, 3]));
    }

    public function testSensorInfo()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorInfo'),
                $this->equalTo(['sensors'   => [1, 2, 3]])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorInfo([1, 2, 3]));
    }

    public function testSensorLog()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('sensorLog'),
                $this->equalTo(['id'   => 1])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->sensorLog(1));
    }

    public function testCameraNear()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('cameraNear'),
                $this->equalTo(['radius'   => 200])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->cameraNear(200));
    }

    public function testCameraNearLatLng()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('cameraNear'),
                $this->equalTo(['radius'   => 200, 'lat'   => 1, 'lng'   => 2])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->cameraNear(200, 1, 2));
    }

    public function testCameraShots()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('cameraShots'),
                $this->equalTo(['id'   => 1])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->cameraShots(1));
    }

    public function testLogin()
    {
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs(['uuid', 'apiKey'])
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('login'),
                $this->equalTo(['login' => 'foo', 'hash'   => md5(strtolower(md5('uuid')) . md5('bar'))])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->login('foo', 'bar'));
    }

    public function testLogout()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('logout')
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->logout());
    }

    public function testObjectWhere()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();

        $client->expects($this->once())
            ->method('request')
            ->with(
                $this->equalTo('objectWhere'),
                $this->equalTo(['imei' => '123456'])
            )
            ->willReturn(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $client->objectWhere('123456'));
    }
}
