<?php


namespace NarodmonApi;

use NarodmonApi\Exceptions\AccessDeniedException;
use NarodmonApi\Exceptions\AuthorizationRequiredException;
use NarodmonApi\Exceptions\BlockedException;
use NarodmonApi\Exceptions\NotFoundException;
use NarodmonApi\Exceptions\ObjectDisabledException;
use NarodmonApi\Exceptions\ServerIsNotAvailableException;
use NarodmonApi\Exceptions\SyntaxErrorException;
use NarodmonApi\Exceptions\TooManyRequestsException;

class Client
{
    private $narodmonUrl = 'http://narodmon.ru/api';

    protected $uuid = null;
    protected $apiKey = null;
    protected $lang = null;

    protected $client = null;

    public function __construct($uuid, $apiKey, $lang = 'ru')
    {
        $this->uuid = strtolower(md5($uuid));
        $this->apiKey = $apiKey;
        $this->lang = $lang;

        $this->client = new \GuzzleHttp\Client();
    }

    protected function request($cmd, $params = [])
    {
        $json = [
            'cmd'       => $cmd,
            'lang'      => $this->lang,
            'uuid'      => $this->uuid,
            'api_key'   => $this->apiKey
        ];

        $json = array_merge(
            $json,
            $params
        );

        $response = $this->client->post($this->narodmonUrl, [
            'json'  =>$json
        ]);

        $result = json_decode($response->getBody(), true);

        if (isset($result['errno'])) {
            switch ($result['errno']) {
                case 400:
                    throw new SyntaxErrorException($result['error'], $result['errno']);
                    break;
                case 401:
                    throw new AuthorizationRequiredException($result['error'], $result['errno']);
                    break;
                case 403:
                    throw new AccessDeniedException($result['error'], $result['errno']);
                    break;
                case 404:
                    throw new NotFoundException($result['error'], $result['errno']);
                    break;
                case 423:
                    throw new BlockedException($result['error'], $result['errno']);
                    break;
                case 429:
                    throw new TooManyRequestsException($result['error'], $result['errno']);
                    break;
                case 434:
                    throw new ObjectDisabledException($result['error'], $result['errno']);
                    break;
                case 503:
                    throw new ServerIsNotAvailableException($result['error'], $result['errno']);
                    break;
                default:
                    throw new \Exception($result['error'], $result['errno']);
                    break;
            }
        }
        return json_decode($response->getBody(), true);
    }

    public function sensorInit()
    {
        $params = [
            'version'   => '1.1',
            'platform'  => '6.0',
        ];

        return $this->request('sensorInit', $params);
    }

    public function getLocation()
    {
        return $this->request('getLocation');
    }

    public function setLocation($lat, $lng)
    {
        $params = [
            'lat'   => $lat,
            'lng'   => $lng
        ];

        return $this->request('setLocation', $params);
    }

    public function mySensors($types = [])
    {
        $params = [
            'my'        => 1,
            'radius'    => 10000,
            'types'    => $types
        ];

        return $this->request('sensorNear', $params);
    }

    public function publicSensors($radius = 100, $types = [], $lat = null, $lng = null)
    {
        $params = [
            'pub'        => 1,
            'radius'    => $radius,
            'types'    => $types
        ];

        if ($lat && $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }

        return $this->request('sensorNear', $params);
    }

    public function sensorNear($my = false, $pub = false, $radius = 100, $types = [], $lat = null, $lng = null)
    {
        $params = [
            'my'        => intval($my),
            'pub'       => intval($pub),
            'radius'    => intval($radius),
            'types'    => $types
        ];

        if ($lat && $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }
        return $this->request('sensorNear', $params);
    }

    public function sensorDev($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('sensorDev', $params);
    }

    public function sensorFav($sensors = null)
    {
        $params = [];

        if (is_array($sensors)) {
            $params['sensors'] = $sensors;
        }

        return $this->request('sensorDev', $params);
    }

    public function sensorInfo(array $sensors)
    {
        $params = [
            'sensors'   => $sensors,
        ];

        return $this->request('sensorInfo', $params);
    }

    public function sensorLog($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('sensorLog', $params);
    }

    public function cameraNear($radius = 100, $lat = null, $lng = null)
    {
        $params = [
            'radius'   => $radius,
        ];

        if ($lat && $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }

        return $this->request('cameraNear', $params);
    }

    public function cameraShots($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('cameraShots', $params);
    }

    public function login($login, $passwd)
    {
        $params = [
            'login' => $login,
            'hash'   => md5($this->uuid . md5($passwd)),
        ];

        return $this->request('login', $params);
    }

    public function logout()
    {
        return $this->request('logout');
    }

    public function objectWhere($imei)
    {
        $params = [
            'imei'  => $imei,
        ];

        return $this->request('objectWhere', $params);
    }
}
