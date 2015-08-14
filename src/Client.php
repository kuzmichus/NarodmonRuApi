<?php
/**
 * PHP version 5.5
 *
 * @category Client
 * @package  NarodmonApi
 * @author   Sergey V. Kuzin <sergey@kuzin.name>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace NarodmonApi;

use NarodmonApi\Exceptions\AccessDeniedException;
use NarodmonApi\Exceptions\AuthorizationRequiredException;
use NarodmonApi\Exceptions\BlockedException;
use NarodmonApi\Exceptions\NotFoundException;
use NarodmonApi\Exceptions\ObjectDisabledException;
use NarodmonApi\Exceptions\ServerIsNotAvailableException;
use NarodmonApi\Exceptions\SyntaxErrorException;
use NarodmonApi\Exceptions\TooManyRequestsException;

/**
 * Class Client
 *
 * @category Client
 * @package  NarodmonApi
 * @author   Sergey V. Kuzin <sergey@kuzin.name>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class Client
{
    /**
     * @var string
     */
    private $narodmonUrl = 'http://narodmon.ru/api';

    /**
     * @var null|string
     */
    protected $uuid = null;
    /**
     * @var null|string
     */
    protected $apiKey = null;
    /**
     * @var null|string
     */
    protected $lang = null;

    /**
     * @var \GuzzleHttp\Client|null
     */
    protected $client = null;

    /** @var \Psr\Cache\CacheItemPoolInterface */
    protected $cache = null;

    /**
     * @param string $uuid
     * @param string $apiKey
     * @param string $lang
     */
    public function __construct($uuid, $apiKey, $lang = 'ru')
    {
        $this->uuid = strtolower(md5($uuid));
        $this->apiKey = $apiKey;
        $this->lang = $lang;

        $this->cache = new \Psr\Cache\NullCacheItemPool();
        $this->client = new \GuzzleHttp\Client();
    }

    public function setCache(\Psr\Cache\CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $cmd    -
     * @param array  $params
     * @return mixed
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
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

        $key = 'narodmon-' . md5(serialize($json));

        $item = $this->cache->getItem($key);
        if ($item->exists()) {
            $result = $item->get();
        } else {
            $response = $this->client->post(
                $this->narodmonUrl, [
                    'json' => $json
                ]
            );

            if (isset($result['errno'])) {
                switch ($result['errno']) {
                    case 400:
                        throw new SyntaxErrorException(
                            $result['error'],
                            $result['errno']
                        );
                    case 401:
                        throw new AuthorizationRequiredException(
                            $result['error'],
                            $result['errno']
                        );
                    case 403:
                        throw new AccessDeniedException(
                            $result['error'],
                            $result['errno']
                        );
                    case 404:
                        throw new NotFoundException(
                            $result['error'],
                            $result['errno']
                        );
                    case 423:
                        throw new BlockedException(
                            $result['error'],
                            $result['errno']
                        );
                    case 429:
                        throw new TooManyRequestsException(
                            $result['error'],
                            $result['errno']
                        );
                    case 434:
                        throw new ObjectDisabledException(
                            $result['error'],
                            $result['errno']
                        );
                    case 503:
                        throw new ServerIsNotAvailableException(
                            $result['error'],
                            $result['errno']
                        );
                    default:
                        throw new \Exception($result['error'], $result['errno']);
                }
            }
            $result = json_decode($response->getBody(), true);
            $item->set($result, 60 * 5);
            $this->cache->save($item);
        }
        return $result;
    }

    /**
     * запрос версии, имени пользователя, местонахождения, избранного и список типов датчиков
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorInit()
    {
        $params = [
            'version'   => '1.1',
            'platform'  => '6.0',
        ];

        return $this->request('sensorInit', $params);
    }

    /**
     * запрос текущего местонахождение пользователя (точки отсчета)
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function getLocation()
    {
        return $this->request('getLocation');
    }

    /**
     * запрос установка нового местонахождение пользователя (точки отсчета)
     *
     * @param $lat
     * @param $lng
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function setLocation($lat, $lng)
    {
        $params = [
            'lat'   => $lat,
            'lng'   => $lng
        ];

        return $this->request('setLocation', $params);
    }

    /**
     * Получение личных датчиков если пользователь авторизован
     *
     * @param array $types
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function mySensors($types = [])
    {
        $params = [
            'my'        => 1,
            'radius'    => 10000,
            'types'    => $types
        ];

        return $this->request('sensorNear', $params);
    }

    /**
     * Получение ближайших публичных датчиков
     *
     * @param array $types
     * @param int   $radius
     * @param float $lat
     * @param float $lng
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function publicSensors($types = [], $radius = 100, $lat = null, $lng = null)
    {
        $params = [
            'pub'        => 1,
            'radius'    => $radius,
            'types'    => $types
        ];

        if (null !== $lat && null !== $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }

        return $this->request('sensorNear', $params);
    }

    /**
     * запрос списка ближайших к пользователю датчиков
     *
     * @param bool|false $my
     * @param bool|false $pub
     * @param array      $types
     * @param int        $radius
     * @param float      $lat
     * @param float      $lng
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorNear($my = false, $pub = false, array $types = [], $radius = 100, $lat = null, $lng = null)
    {
        $params = [
            'my'        => intval($my),
            'pub'       => intval($pub),
            'radius'    => intval($radius),
            'types'    => $types
        ];

        if (null !== $lat && null !== $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }
        return $this->request('sensorNear', $params);
    }

    /**
     * запрос списка датчиков и их показаний по ID устр-ва мониторинга
     *
     * @param int $id
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorDev($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('sensorDev', $params);
    }

    /**
     * список избранных датчиков и их показаний для авторизованного пользователя
     *
     * @param array $sensors
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorFav(array $sensors = [])
    {
        $params = [];

        if (!empty($sensors)) {
            $params['sensors'] = $sensors;
        }

        return $this->request('sensorFav', $params);
    }

    /**
     * регулярное обновление показаний выбранных датчиков
     *
     * @param array $sensors
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorInfo(array $sensors)
    {
        $params = [
            'sensors'   => $sensors,
        ];

        return $this->request('sensorInfo', $params);
    }

    /**
     * история показаний датчика за период (для графиков)
     *
     * @param $id
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function sensorLog($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('sensorLog', $params);
    }

    /**
     * запрос списка ближайших к пользователю веб-камер
     *
     * @param int $radius
     * @param int $lat
     * @param int $lng
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function cameraNear($radius = 100, $lat = null, $lng = null)
    {
        $params = [
            'radius'   => $radius,
        ];

        if (null !== $lat && null !== $lng) {
            $params['lat']  = $lat;
            $params['lng']  = $lng;
        }

        return $this->request('cameraNear', $params);
    }

    /**
     * запрос списка снимков с веб-камеры по ее ID
     *
     * @param int $id - ID веб-камеры из ссылки вида http://narodmon.ru/-ID в балуне на карте.
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function cameraShots($id)
    {
        $params = [
            'id'   => $id,
        ];

        return $this->request('cameraShots', $params);
    }

    /**
     * авторизация пользователя при вводе логина и пароля
     *
     * @param string $login
     * @param string $passwd
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function login($login, $passwd)
    {
        $params = [
            'login' => $login,
            'hash'   => md5($this->uuid . md5($passwd)),
        ];

        return $this->request('login', $params);
    }

    /**
     * завершение сеанса текущего пользователя
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function logout()
    {
        return $this->request('logout');
    }

    /**
     * определение местонахождения объекта с GPS
     *
     * @param string $imei 15-значный числовой серийный номер устр-ва, указанный в Мои GPS
     *
     * @return array
     *
     * @throws AccessDeniedException
     * @throws AuthorizationRequiredException
     * @throws BlockedException
     * @throws NotFoundException
     * @throws ObjectDisabledException
     * @throws ServerIsNotAvailableException
     * @throws SyntaxErrorException
     * @throws TooManyRequestsException
     * @throws \Exception
     */
    public function objectWhere($imei)
    {
        $params = [
            'imei'  => $imei,
        ];

        return $this->request('objectWhere', $params);
    }
}
