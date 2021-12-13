<?php

namespace LapayGroup\FivePostSdk;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use LapayGroup\FivePostSdk\Entity\Order;
use LapayGroup\FivePostSdk\Exceptions\FivePostException;
use LapayGroup\FivePostSdk\Exceptions\TokenException;
use LapayGroup\FivePostSdk\Helpers\JwtSaveInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use TariffsTrait;

    /** @var string|null */
    private $jwt = null;

    /** @var string|null */
    private $api_key = null;

    /** @var \GuzzleHttp\Client|null */
    private $httpClient = null;

    /** @var JwtSaveInterface|null */
    private $jwtHelper = null;


    const API_URI_TEST = 'https://api-preprod-omni.x5.ru';
    const API_URI_PROD = 'https://api-omni.x5.ru';

    const DATA_JSON   = 'json';
    const DATA_PARAMS = 'form_params';

    /**
     * Client constructor.
     *
     * @param string $api_key - APIKEY в системе 5post
     * @param int $timeout - таймаут ожидания ответа от серверов 5post в секундах
     * @param string $api_uri - адрес API (тествоый или продуктовый)
     * @param JwtSaveInterface|null $jwtHelper - помощник для сохранения токена
     */
    public function __construct($api_key, $timeout = 300, $api_uri = self::API_URI_PROD, $jwtHelper = null)
    {
        $this->api_key = $api_key;
        $this->stack = new HandlerStack();
        $this->stack->setHandler(new CurlHandler());
        $this->stack->push($this->handleAuthorizationHeader());

        $this->httpClient = new \GuzzleHttp\Client([
            'handler'  => $this->stack,
            'base_uri' => $api_uri,
            'timeout' => $timeout,
            'exceptions' => false
        ]);

        if ($jwtHelper)
            $this->jwtHelper = $jwtHelper;
    }

    /**
     * Инициализирует вызов к API
     *
     * @param $type
     * @param $method
     * @param array $params
     * @return array
     * @throws FivePostException
     */
    private function callApi($type, $method, $params = [], $data_type = self::DATA_JSON)
    {
        switch ($type) {
            case 'DELETE':
                $request = http_build_query($params);
                if ($this->logger) {
                    $this->logger->info("5Post {$type} API request {$method}: " . $request);
                }
                $response = $this->httpClient->delete($method, ['query' => $params]);
                break;
            case 'POST':
                $request = json_encode($params);
                if ($this->logger) {
                    $this->logger->info("5Post API {$type} request {$method}: " . $request);
                }
                $response = $this->httpClient->post($method, [$data_type => $params]);
                break;
        }

        $json = $response->getBody()->getContents();

        if ($this->logger) {
            $headers = $response->getHeaders();
            $headers['http_status'] = $response->getStatusCode();
            $this->logger->info("5Post API response {$method}: " . $json, $headers);
        }

        $resp5post = json_decode($json, true);

        if (empty($resp5post) && $json != '[]')
            throw new FivePostException('От сервера 5Post при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $json, $request);

        if (!empty($resp5post['fault']))
            throw new FivePostException('От сервера 5Post при вызове метода ' . $method . ' получена ошибка: ' . $resp5post['fault']['faultstring'], $response->getStatusCode(), $json, $request);

        if (!empty($resp5post['error']))
            throw new FivePostException('От сервера 5Post при вызове метода ' . $method . ' получена ошибка: ' . $resp5post['error'].', '.$resp5post['message'], $resp5post['status'], $json, $request);

        if (!empty($resp5post['status']) && !in_array($resp5post['status'], ['OK', 'ok'])) {
            if (empty($resp5post['description'])) $resp5post['description'] = '';
            if (empty($resp5post['id'])) $resp5post['id'] = '';
            throw new FivePostException('От сервера 5Post при вызове метода ' . $method . ' получена ошибка: ' . $resp5post['description']. '('.$resp5post['id'].')', $response->getStatusCode(), $json, $request);
        }

        return $resp5post;
    }

    /**
     * @return \Closure
     */
    private function handleAuthorizationHeader()
    {
        return function (callable $handler)
        {
            return function (RequestInterface $request, array $options) use ($handler)
            {
                if ($this->jwt) {
                    $request = $request->withHeader('Authorization', 'Bearer ' . $this->jwt);
                }

                return $handler($request, $options);
            };
        };
    }

    public function getJwt()
    {
        if ($this->jwtHelper)
            $this->jwt = $this->jwtHelper->getToken();

        if ($this->jwt) {
            try {
                Jwt::decode($this->jwt);
            }

            catch (TokenException $e) {
                $this->jwt = $this->generateJwt();
            }
        } else {
            $this->jwt = $this->generateJwt();
        }

        Jwt::decode($this->jwt);

        return $this->jwt;
    }

    /**
     * @param string $jwt - ранее полученный JWT токен
     */
    public function setJwt($jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Получение JWT токена по api-key
     *
     * @return mixed
     * @throws FivePostException
     */
    private function generateJwt()
    {
        $response = $this->callApi('POST', '/jwt-generate-claims/rs256/1?apikey='.$this->api_key, ['subject' => 'OpenAPI', 'audience' => 'A122019!'], self::DATA_PARAMS);

        if ($this->jwtHelper)
            $this->jwt = $this->jwtHelper->setToken($response['jwt']);

        return $response['jwt'];
    }

    /**
     * Возврат списка ПВЗ
     *
     * @param int $number - Номер страницы (нумерация начинается с 0)
     * @param int $size - Количество точек выдачи на странице
     * @throws FivePostException
     */
    public function getPvzList($number = 0, $size = 1000)
    {
        return $this->callApi('POST', '/api/v1/pickuppoints/query', ['pageSize' => $size, 'pageNumber' => $number]);
    }

    /**
     * Создание складов забора заказов
     *
     * @param $warhouse_list
     * @return array
     * @throws FivePostException
     */
    public function addWarehouses($warhouse_list)
    {
        return $this->callApi('POST', '/api/v1/warehouse', $warhouse_list);
    }

    /**
     * Создание заказов
     *
     * @param array $order_list - массив объектов Order
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     */
    public function createOrders($order_list)
    {
        $params = [];
        $params['partnerOrders'] = [];

        /** @var Order $order */
        foreach ($order_list as $order) {
            $params['partnerOrders'][] = $order->asArr();
        }

        return $this->callApi('POST', '/api/v1/createOrder', $params);
    }

    /**
     * Отмена заказа
     *
     * @param string $order_id
     * @return array
     * @throws FivePostException
     */
    public function cancelOrder($order_id)
    {
        return $this->callApi('DELETE', '/api/v1/cancelOrder/'.$order_id);
    }

    /**
     * Информация о статусе заказов
     *
     * @param array $order_id_list - массив id заказов (в каждом элементе ожидается подмассив с ключами order_id или vendor_id)
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     * @deprecated Будет удален в версии 0.6.0 используйте новые методы getOrdersStatusByOrderId и getOrdersStatusByVendorId
     */
    public function getOrdersStatus($order_id_list)
    {
        $params = [];
        foreach ($order_id_list as $i => $info) {
            $param = [];
            if (!empty($info['vendor_id'])) $param['orderId'] = $info['vendor_id'];
            if (!empty($info['order_id'])) $param['senderOrderId'] = $info['order_id'];
            if (empty($param))
                throw new \InvalidArgumentException('В элементе массив '.$i.' отсутствуют параметры vendor_id и order_id');

            $params[] = $param;
        }

        return $this->callApi('POST', '/api/v1/getOrderStatus', $params);
    }

    /**
     * Информация о статусе заказов по ID клиента
     *
     * @param array $order_id_list - массив ID заказов в системе клиента
     * @return array
     * @throws FivePostException
     */
    public function getOrdersStatusByOrderId($order_id_list)
    {
        $params = [];
        foreach ($order_id_list as $i => $order_id) {
            $params[$i]['senderOrderId'] = $order_id;
        }

        return $this->callApi('POST', '/api/v1/getOrderStatus', $params);
    }

    /**
     * Информация о статусе заказов по ID клиента
     *
     * @param array $vendor_id_list - массив ID заказов в системе 5post
     * @return array
     * @throws FivePostException
     */
    public function getOrdersStatusByVendorId($vendor_id_list)
    {
        $params = [];
        foreach ($vendor_id_list as $i => $vendor_id) {
            $params[$i]['orderId'] = $vendor_id;
        }

        return $this->callApi('POST', '/api/v1/getOrderStatus', $params);
    }

    /**
     * История статусов заказа
     *
     * @param string|null $order_id - ID заказа в системе клиента
     * @param string|null $vendor_id - ID заказа в системе 5post
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     * @deprecated Будет удален в версии 0.5.0 используйте новые методы getOrderStatusesByOrderId и getOrderStatusesByVendorId
     */
    public function getOrderStatuses($order_id = null, $vendor_id = null)
    {
        if (empty($order_id) && empty($vendor_id))
            throw new \InvalidArgumentException('Отсутствует обязательный параметр order_id или vendor_id');

        $params = [];
        if (!empty($vendor_id)) $params['orderId'] = $vendor_id;
        if (!empty($order_id)) $params['senderOrderId'] = $order_id;

        return $this->callApi('POST', '/api/v1/getOrderHistory', $params);
    }


    /**
     * История статусов заказа
     *
     * @param string $order_id - ID заказа в системе клиента
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     */
    public function getOrderStatusesByOrderId($order_id)
    {
        if (empty($order_id))
            throw new \InvalidArgumentException('Отсутствует обязательный параметр order_id');

        return $this->callApi('POST', '/api/v1/getOrderHistory', ['senderOrderId' => $order_id]);
    }


    /**
     * История статусов заказа
     *
     * @param string $vendor_id - ID заказа в системе 5post
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     */
    public function getOrderStatusesByVendorId($vendor_id)
    {
        if (empty($vendor_id))
            throw new \InvalidArgumentException('Отсутствует обязательный параметр vendor_id');

        return $this->callApi('POST', '/api/v1/getOrderHistory', ['orderId' => $vendor_id]);
    }

    /**
     * История статусов заказов по ID заказа в системе клиента
     *
     * @param string[] $order_ids - Список ID заказов в системе клиента
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     */
    public function getOrderStatusesByListOrderIds($order_ids)
    {
        if (empty($order_ids))
            throw new \InvalidArgumentException('Отсутствует обязательный параметр order_ids');

        return $this->callApi('POST', '/api/v1/getOrderHistoryMass/bySenderOrderId', ['senderOrderIdList' => $order_ids]);
    }

    /**
     * История статусов заказов по ID заказа в системе 5post
     *
     * @param string[] $vendor_ids - Список ID заказов в системе 5post
     * @return array
     * @throws FivePostException
     * @throws \InvalidArgumentException
     */
    public function getOrderStatusesByListVendorIds($vendor_ids)
    {
        if (empty($vendor_ids))
            throw new \InvalidArgumentException('Отсутствует обязательный параметр vendor_ids');

        return $this->callApi('POST', '/api/v1/getOrderHistoryMass/byOrderId', ['orderIdList' => $vendor_ids]);
    }
}