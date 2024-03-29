
<a href="https://lapaygroup.ru/"><img align="left" width="200" src="https://lapaygroup.ru/img/lapaygroup.svg"></a>
<a href="http://fivepost.ru"><img align="right" width="200" src="https://lapaygroup.ru/fivepostlogo.png"></a>    

<br /><br /><br />

[![Latest Stable Version](https://poser.pugx.org/lapaygroup/fivepost-sdk/v/stable)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![Total Downloads](https://poser.pugx.org/lapaygroup/fivepost-sdk/downloads)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![License](https://poser.pugx.org/lapaygroup/fivepost-sdk/license)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![Telegram Chat](https://img.shields.io/badge/telegram-chat-blue.svg?logo=telegram)](https://t.me/phpboxberrysdk)

# SDK для [интеграции с программным комплексом 5post](http://fivepost.ru).  

Посмотреть все проекты или подарить автору кофе можно [тут](https://lapaygroup.ru/opensource).    

[Документация к API](https://fivepost.ru/developers) 5post.    

# Содержание    
- [Changelog](#changelog)    
- [Конфигурация](#configuration)  
- [Отладка](#debugging)  
- [Расчет тарифа](#tariffs)  
- [Список точек выдачи](#pvz-list)  
- [Создание склада](#create-warehouse)   
- [Создание заказа](#create-order)   
- [Отмена заказа](#cancel-order)   
- [Статусы заказов](#orders-status)   
- [История статусов заказа](#order-statuses)     
- [Получение чеков](#receipts)    
- [Получение этикетки](#order-labels)    
- [Обнуление наложенного платежа](#np-zero)    


<a name="links"><h1>Changelog</h1></a>
- 0.6.2 - Добавлены зависимости guzzle ^7.0 и psr/log ^2.0|^3.0
- 0.6.1 - Обновлеты тарифы 5post для расчёта;  
- 0.6.0 - Подробное описание [тут](https://github.com/lapaygroup/fivepost-sdk/releases/tag/0.6.0);    
- 0.5.0 - Подробное описание [тут](https://github.com/lapaygroup/fivepost-sdk/releases/tag/0.5.0);
- 0.4.6 - Совместимость с Guzzle 7.3;   
- 0.4.5 - Добавлен вывод неизвестного кода статуса в текст исключения. Добавлена заменя executionStatus, если там присутствует описание вместе с кодом;   
- 0.4.4 - Совместимость с Guzzle 7.2;
- 0.4.3 - Совместимость с Guzzle 7.1;    
- 0.4.2 - Совместимость с Guzzle 7;  
- 0.4.1 - Исправлены ошибки в namespace файлов;  
- 0.4.0 - Добавлена возможность сохранения JWT токена и создания своих классов для сохранения;  
- 0.3.0 - Добавлен [Enum](https://github.com/lapaygroup/fivepost-sdk/blob/master/src/Enum/OrderStatus.php) статусов заказа, изменены функции работы с статусами заказов;  
- 0.2.1 - Исправлена ошибка с обязательным заполнение налоговой ставки у места;
- 0.2.0 - Добавлен расчет тарифа;
- 0.1.3 - Исправление в composer.json;
- 0.1.2 - Исправление опечаток;  
- 0.1.1 - Исправлена зависимость с Monolog;  
- 0.1.0 - Первая Alfa-версия SDK.  

# Установка  
Для установки можно использовать менеджер пакетов Composer

    composer require lapaygroup/fivepost-sdk
    

<a name="configuration"><h1>Конфигурация</h1></a>  

Для работы с API необходимо получить api-key у персонального менеджера при заключении договора.    
По api-key необходимо получить токен в формате JWT и сохранить его. Токен живет 1 час с момента издания.     

SDK позволяет сохранять JWT, для этого необходимо использовать Helper, который должен реализовывать [JwtSaveInterface](https://github.com/lapaygroup/fivepost-sdk/blob/master/src/Helpers/JwtSaveInterface.php).    
В SDK встроен Helper для сохранения токена в временный файл [JwtSaveFileHelper](https://github.com/lapaygroup/fivepost-sdk/blob/master/src/Helpers/JwtSaveFileHelper.php).    

```php
try {
    // Инициализация API клиента по api-key с таймаутом ожидания ответа 60 секунд
    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
    $jwt = $Client->getJwt(); // $jwt = eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJPcGVuQVBJIiwiYXVkIjoiQTEyMjAxOSEiLCJhcGlrZXkiOiJBSlMxU0lTRHJrNmRyMFpYazVsZVQxdFBGZDRvcXNIYSIsImlzcyI6InVybjovL0FwaWdlZSIsInBhcnRuZXJJZCI6ImIyNzNlYzQ0LThiMDAtNDliMS04OWVlLWQ4Njc5NjMwZDk0OCIsImV4cCI6MTU5NzA4OTk1OCwiaWF0IjoxNTk3MDg2MzU4LCJqdGkiOiI4YTIyZmUzNy1mMzc0LTQ0NDctOGMzMC05N2ZiYjJjOGQ3MTkifQ.G_XQ6vdk7bXfIeMJer7z5WUFqnwlp0qUt6RxaCINZt3b97ZUwPMI1-1FNKQhFwmCHJGpTYyBJKHgtY3uJZOWDAszjPMIHrQrcnJLSzJisNiy6z3cMbpf-UgD-RgebuaYyEgZ81rekL5aUN6r5rqWHbxcxEGY22lTy9uEWwxF_-UdVLEW9O9Z9M9IMlL5_7ACVu-ID2n6zFk_QJnEumJcBSqb6JFh2TWvUPnjnUt5AOiD7gNRXKsBvoC6InSfGoMA461cxu-rAazhNq5fkqFSdrIUyz0kvAb3UI4hs_6xJy9tXPpXIQY7LQUZqQGp5BT8pasfhAJ_4CCATbqxIHmY9w
    $result = \LapayGroup\FivePostSdk\Jwt::decode($jwt); // Получения информации из токена (payload)

    // Ранее полученный токен можно добавить в клиент специльным методом
    $Client->setJwt($jwt);

    // Токен можно сохранять в файл используя Helper
    $jwtHelper = new \LapayGroup\FivePostSdk\Helpers\JwtSaveFileHelper();
    // Можно задать путь до временного файла отличный от заданного по умолчанию
    $jwtHelper->setTmpFile('/tmp/saved_jwt.txt');

    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST, $jwtHelper);
    $jwt = $Client->getJwt(); // $jwt = eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJPcGVuQVBJIiwiYXVkIjoiQTEyMjAxOSEiLCJhcGlrZXkiOiJBSlMxU0lTRHJrNmRyMFpYazVsZVQxdFBGZDRvcXNIYSIsImlzcyI6InVybjovL0FwaWdlZSIsInBhcnRuZXJJZCI6ImIyNzNlYzQ0LThiMDAtNDliMS04OWVlLWQ4Njc5NjMwZDk0OCIsImV4cCI6MTU5NzA4OTk1OCwiaWF0IjoxNTk3MDg2MzU4LCJqdGkiOiI4YTIyZmUzNy1mMzc0LTQ0NDctOGMzMC05N2ZiYjJjOGQ3MTkifQ.G_XQ6vdk7bXfIeMJer7z5WUFqnwlp0qUt6RxaCINZt3b97ZUwPMI1-1FNKQhFwmCHJGpTYyBJKHgtY3uJZOWDAszjPMIHrQrcnJLSzJisNiy6z3cMbpf-UgD-RgebuaYyEgZ81rekL5aUN6r5rqWHbxcxEGY22lTy9uEWwxF_-UdVLEW9O9Z9M9IMlL5_7ACVu-ID2n6zFk_QJnEumJcBSqb6JFh2TWvUPnjnUt5AOiD7gNRXKsBvoC6InSfGoMA461cxu-rAazhNq5fkqFSdrIUyz0kvAb3UI4hs_6xJy9tXPpXIQY7LQUZqQGp5BT8pasfhAJ_4CCATbqxIHmY9w
        
}

catch (\LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
    // Обработка ошибки вызова API 5post
    // $e->getMessage(); текст ошибки 
    // $e->getCode(); http код ответа сервиса 5post
    // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
}

catch (\Exception $e) {
    // Обработка исключения
}
```


<a name="debugging"><h1>Отладка</h1></a>  
Для логирования запросов и ответов используется [стандартный PSR-3 логгер](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md). 
Ниже приведен пример логирования используя [Monolog](https://github.com/Seldaek/monolog).  

```php
<?php
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    
    $log = new Logger('name');
    $log->pushHandler(new StreamHandler('log.txt', Logger::INFO));

    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
    $Client->setLogger($log);
    $jwt = $Client->getJwt(); // $jwt = eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJPcGVuQVBJIiwiYXVkIjoiQTEyMjAxOSEiLCJhcGlrZXkiOiJBSlMxU0lTRHJrNmRyMFpYazVsZVQxdFBGZDRvcXNIYSIsImlzcyI6InVybjovL0FwaWdlZSIsInBhcnRuZXJJZCI6ImIyNzNlYzQ0LThiMDAtNDliMS04OWVlLWQ4Njc5NjMwZDk0OCIsImV4cCI6MTU5NzA4OTk1OCwiaWF0IjoxNTk3MDg2MzU4LCJqdGkiOiI4YTIyZmUzNy1mMzc0LTQ0NDctOGMzMC05N2ZiYjJjOGQ3MTkifQ.G_XQ6vdk7bXfIeMJer7z5WUFqnwlp0qUt6RxaCINZt3b97ZUwPMI1-1FNKQhFwmCHJGpTYyBJKHgtY3uJZOWDAszjPMIHrQrcnJLSzJisNiy6z3cMbpf-UgD-RgebuaYyEgZ81rekL5aUN6r5rqWHbxcxEGY22lTy9uEWwxF_-UdVLEW9O9Z9M9IMlL5_7ACVu-ID2n6zFk_QJnEumJcBSqb6JFh2TWvUPnjnUt5AOiD7gNRXKsBvoC6InSfGoMA461cxu-rAazhNq5fkqFSdrIUyz0kvAb3UI4hs_6xJy9tXPpXIQY7LQUZqQGp5BT8pasfhAJ_4CCATbqxIHmY9w
    $result = \LapayGroup\FivePostSdk\Jwt::decode($jwt);
```

В log.txt будут логи в виде:
```
[2020-08-10T10:19:15.236829+00:00] 5post-api.INFO: 5Post API POST request /api/v1/getOrderStatus: [{"senderOrderId":"1234567891"}] [] []
[2020-08-10T10:19:15.447289+00:00] 5post-api.INFO: 5Post API response /api/v1/getOrderStatus: [{"status":"REJECTED","orderId":"c1ba069d-a1aa-49ae-a562-3dca429823f4","senderOrderId":"1234567891","executionStatus":"REJECTED: Ошибка валидации по плановым ВГХ","changeDate":"2020-08-10T13:12:43.31964+03:00"}] {"Date":["Mon, 10 Aug 2020 10:19:15 GMT"],"Content-Type":["application/json"],"Connection":["keep-alive"],"Set-Cookie":["d46d09d93a8e8174c6300478f336d992=faa2eebdd2dfdd0ce10a284ccfcbdaea; path=/; HttpOnly; Secure","TS01ab71c3=01a93f75476aa0457856d9bf9d665b19c12ba5ec238a9a08d7fe077961dddc634278c540aed3dd2010a567571da4de02529e7117e9f863d3ac1f9716c0e5a25f8446c685c6; Path=/; Domain=.api-omni.x5.ru"],"Access-Control-Allow-Origin":[""],"Access-Control-Allow-Headers":["origin, x-requested-with, accept, content-type, authorization"],"Access-Control-Max-Age":["3628800"],"Access-Control-Allow-Credentials":["true"],"Access-Control-Allow-Methods":["GET, PUT, POST, DELETE"],"strict-transport-security":["max-age=31536000"],"x-frame-options":["SAMEORIGIN"],"Transfer-Encoding":["chunked"],"http_status":200} []

```


<a name="tariffs"><h1>Расчет тарифа</h1></a>  
Тарифы рассчитываются по тарифным зонам по данным на [сайте 5post](https://fivepost.ru/). 
При заключении договора могут быть индивидуальные тарифы, их можно применить используя метод **setZoneTariffs** в объекте **LapayGroup\FivePostSdk\Client**.
Этот метод принимает массив тарифов [в таком виде](https://github.com/lapaygroup/fivepost-sdk/blob/master/src/TariffsTrait.php#L12).

Для изменения тарифа на услуги используйте методы ниже:

```php
<?php
    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
    // Тариф за возврат невыкупленных отправлений и обработку и возврат отмененных отправлений
    $Client->setReturnPercent(0.5); // 50%    

    // Сбор за объявленную ценность
    $Client->setValuatedAmountPercent(0.005); // 0.5%

    // Вознаграждение за прием платежа с использованием банковских карт
    $Client->setCardPercent(0.0264); // 2.64%

    // Вознаграждение за прием наложенного платежа наличными
    $Client->setCashPercent(0.0192); // 1.92%
```

Для расчета стоимости и сроков доставки используйте метод **calculationTariff**.   
 
**Входные параметры:**
- *$zone* - тарифная зона;  
- *$weight* - вес заказа в граммах;  
- *$amount* - выкупная стоимость заказа;
- *$payment_type* - способ оплаты (оплачен/картой/наличными);
- *$returned* - Возврат в случае невыкупа.

**Выходные параметры:**
- *array* - стоимость доставки и срок доставки в днях

**Примеры вызова:**
```php
<?php
    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
    
    // Доставка в 1 тарифную зону весом 1 кг, предоплаченная, невозвратная
    $tariff = $Client->calculationTariff(1, 1000, 0, \LapayGroup\FivePostSdk\Entity\Order::P_TYPE_PREPAYMENT, false);

    // Доставка в 1 тарифную зону весом 4 кг, предоплаченная, невозвратная
    $tariff = $Client->calculationTariff(1, 4000, 0, \LapayGroup\FivePostSdk\Entity\Order::P_TYPE_PREPAYMENT, false);

    // Доставка в 1 тарифную зону весом 4 кг, предоплаченная, c возвратом в случае невыкупа
    $tariff = $Client->calculationTariff(1, 4000, 0, \LapayGroup\FivePostSdk\Entity\Order::P_TYPE_PREPAYMENT, true);

    // Доставка в 1 тарифную зону весом 2 кг, стоимостью 1000 рублей, невозвратная, оплата наличными
    $tariff = $Client->calculationTariff(1, 2000, 1000, \LapayGroup\FivePostSdk\Entity\Order::P_TYPE_CASH, false);

    // Доставка в 1 тарифную зону весом 2 кг, стоимостью 1000 рублей, невозвратная, оплата картой
    $tariff = $Client->calculationTariff(1, 2000, 1000, \LapayGroup\FivePostSdk\Entity\Order::P_TYPE_CASHLESS, false);

    /*
     array (
          [price] => 1000.89 // float стоимость доставки в рублях
          [delivery_days] => 6 // int количество дней
     )
     */
```

<a name="pvz-list"><h1>Список точек выдачи</h1></a>   
Метод **getPvzList** возвращает список постаматов и пунктов выдачи заказов.   
Рекомендуем запрашивать не более 500 точек на странице.

**Входные параметры:**
- *int $number* - Номер страницы / среза (нумерация начинается с 0);
- *int $size* - Количество точек выдачи на странице / срезе.

**Выходные параметры:**
- *array* - срез ПВЗ из справочника с данными среза.

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        $totalPages = 2;
        foreach ($i = 0; $i <= $totalPages; $i++) {
            $result = $Client->getPvzList(0, 1000); // Больше 2000 за раз получить нельзя
            if (!empty($result['totalPages'])) $totalPages = $result['totalPages']; // Заносим количество страниц из ответа
        }
        /**
            Array
            (
                [content] => Array
                    (
                        [0] => Array
                            (
                                [id] => 001c8a44-dac3-4651-8a9e-caa8cdbd860e
                                [mdmCode] => OM120349
                                [name] => OM120349
                                [partnerName] => Fivebox
                                [multiplaceDeliveryAllowed] =>
                                [type] => POSTAMAT
                                [workHours] => Array
                                    (
                                        [0] => Array
                                            (
                                                [day] => MON
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [1] => Array
                                            (
                                                [day] => TUE
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [2] => Array
                                            (
                                                [day] => WED
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [3] => Array
                                            (
                                                [day] => THU
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [4] => Array
                                            (
                                                [day] => FRI
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [5] => Array
                                            (
                                                [day] => SAT
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                        [6] => Array
                                            (
                                                [day] => SUN
                                                [opensAt] => 08:00:00
                                                [closesAt] => 22:00:00
                                                [timezone] => Europe/Moscow
                                                [timezoneOffset] => +03:00
                                            )
            
                                    )
            
                                [fullAddress] => Зеленодольск г, Татарстан ул., 30
                                [shortAddress] => Татарстан ул., 30
                                [address] => Array
                                    (
                                        [country] => Россия
                                        [zipCode] => 422544
                                        [region] => Татарстан республика
                                        [city] => Зеленодольск г
                                        [regionType] => республика
                                        [cityType] => г
                                        [street] => Татарстан ул.
                                        [house] => 30
                                        [building] =>
                                        [metroStation] =>
                                        [lat] => 55.854559
                                        [lng] => 48.503302
                                    )
            
                                [additional] => Постамат 5post расположен в магазине «Пятёрочка»
                                [cellLimits] => Array
                                    (
                                        [maxCellWidth] => 457
                                        [maxCellHeight] => 322
                                        [maxCellLength] => 400
                                        [maxWeight] => 15000000
                                    )
            
                                [returnAllowed] =>
                                [timezoneOffset] => +03:00
                                [phone] => 88005118800
                                [cashAllowed] =>
                                [cardAllowed] =>
                                [loyaltyAllowed] =>
                                [extStatus] => ACTIVE
                                [localityFiasCode] => 1e90ec38-0f85-442b-ac1b-ca687fa91d88
                                [createDate] => 2020-03-20T15:40:12.223556+03:00
                                [deliverySL] => Array
                                    (
                                        [0] => Array
                                            (
                                                [sl] => 4
                                                [slCode] => 10
                                            )
            
                                    )
            
                                [rate] => Array
                                    (
                                        [0] => Array
                                            (
                                                [rateType] =>
                                                [zone] => 5
                                                [rateValue] => 199.17
                                                [vat] => 20
                                                [rateValueWithVat] => 239.004
                                                [rateExtraValueWithVat] => 36
                                                [rateExtraValue] => 30
                                                [rateCurrency] => RUB
                                                [rateTypeCode] => 10
                                            )
            
                                    )
            
                                [lastMileWarehouse] => Array
                                    (
                                        [id] => ab64adfe-4774-453c-9eb7-f89534804530
                                        [name] => РЦ Казань
                                    )
            
                                [openDate] => 2018-11-22T15:07:35.980608
                                [timezone] => Europe/Moscow
                            )
            
                    )
            
                [pageable] => Array
                    (
                        [sort] => Array
                            (
                                [unsorted] =>
                                [sorted] => 1
                                [empty] =>
                            )
            
                        [pageNumber] => 0
                        [pageSize] => 1
                        [offset] => 0
                        [paged] => 1
                        [unpaged] =>
                    )
            
                [totalElements] => 17315
                [totalPages] => 17315
                [last] =>
                [sort] => Array
                    (
                        [unsorted] =>
                        [sorted] => 1
                        [empty] =>
                    )
            
                [numberOfElements] => 1
                [first] => 1
                [size] => 1
                [number] => 0
                [empty] =>
            )
        **/
    }

    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  

    catch (\Exception $e) {
        // Обработка исключения
    }
```

<a name="create-warehouse"><h1>Создание склада</h1></a>  
Метод **addWarehouses** позволяет добавить склад забора заказов. За один запрос можно добавить несколько складов.  

**Входные параметры:**
- *Warehouse[]* - массив объектов [LapayGroup\FivePostSdk\Entity\Warehouse](src/Entity/Warehouse.php).

**Выходные параметры:**
- *array* - Результат создания складов

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        $warehouse = new \LapayGroup\FivePostSdk\Entity\Warehouse();
        $warehouse->setId('WH001');
        $warehouse->setName('Склад ООО Ромашка');
        $warehouse->setCountryId('RU');
        $warehouse->setRegionCode(77);
        $warehouse->setFederalDistrict('Москва');
        $warehouse->setRegion('Москва');
        $warehouse->setZipCode(111024);
        $warehouse->setCity('Москва');
        $warehouse->setStreet('ул. 5 Кабельная');
        $warehouse->setHouse('1');
        $warehouse->setLatitude('55.123456');
        $warehouse->setLongitude('37.123456');
        $warehouse->setPhone('+74951234567');
        $warehouse->setTimeZone('+03:00');
    
        for ($i = 1; $i < 6; $i++) {
            $workDay = new \LapayGroup\FivePostSdk\Entity\WorkingDay();
            $workDay->setDay($i); // 1 - понедельник, 7 - воскресенье
            $workDay->setTimeFrom('08:00:00');
            $workDay->setTimeTill('17:00:00');
            $warehouse->setWorkingDay($workDay);
        }
    
        $result = $Client->addWarehouses([$warehouse->asArr()]);
        /** Успешный ответ
        Array
        (
            [0] => Array
                (
                    [id] => 485cee56-a0e8-306b-ffc0-93bed958307a
                    [status] => OK
                    [description] =>
                )

        )
        **/
        
        /** Ответ при ошибке
        Array
        (
            [0] => Array
                (
                    [id] => ab58e4de-8bc8-0c2e-ac07-4ab458cca5a3
                    [status] => FAILED
                    [description] => Склад с таким идентификатором уже существует (partnreLocationId = 123456)
                )

        )
        **/


    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
````


<a name="create-order"><h1>Создание заказа</h1></a>  
Метод **createOrders** позволяет создать многоместный заказ (v3 API, метод включается по запросу к персональному менеджеру). За один запрос можно создать несколько заказов.   
Метод **createOrdersV1** реализует создание одноместных заказов через v1 API.   

**Входные параметры:**
- *Order[]* - массив объектов [LapayGroup\FivePostSdk\Entity\Order](src/Entity/Order.php).

**Выходные параметры:**
- *array* - Результат создания заказов

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        $Order = new \LapayGroup\FivePostSdk\Entity\Order();
        $Order->setId('1234567892');
        $Order->setCompanyName('Ромашка');
        $Order->setNumber('ORD-123456');
        $Order->setFio('Иванов Иван Иванович');
        $Order->setPhone('89260120934');
        $Order->setEmail('test@test.ru');
        $Order->setPaymentValue(0);
        $Order->setPaymentCur('RUB');
        $Order->setPaymentType(\LapayGroup\FivePostSdk\Entity\Order::P_TYPE_PREPAYMENT);
        $Order->setPrice(1000);
        $Order->setPriceCur('RUB');
        $Order->setPvzId('00598559-f57b-4e23-9891-0d6f60bc455c');
        $Order->setWarehouseId('WH001');
        $Order->setShipmentDate(new DateTime('2020-08-15'));
        $Order->setUndeliverableOption(\LapayGroup\FivePostSdk\Entity\Order::UNDELIVERED_RETURN);
    
        $Place = new \LapayGroup\FivePostSdk\Entity\Place();
        $Place->setBarcode('32270000000001');
        $Place->setId('11300000294');
        $Place->setPrice(1000);
        $Place->setVatRate(20);
        $Place->setCurrency('RUB');
        $Place->setHeight(20);
        $Place->setLength(130);
        $Place->setWidth(60);
        $Place->setWeight(100000);
    
        $item = new \LapayGroup\FivePostSdk\Entity\Item();
        $item->setBarcode('32270000000001');
        $item->setQuantity(1);
        $item->setCodeGtg('1020911016032000003592');
        $item->setName('Силиконовый чехол для iPhone XS');
        $item->setPrice(1000);
        $item->setCurrency('RUB');
        $item->setVatRate(20);
        $item->setArticul('978-5-00154-080-9');
    
        $Place->setItem($item);
        $Order->setPlace($Place);
    
        $result = $Client->createOrdersV1([$Order]); // Создание заказов без мест V1
        $result = $Client->createOrders([$Order]); // Создание многоместных заказов V3
        
        /** Успешный ответ
        Array
        (
            [0] => Array
                (
                    [orderId] => 12854f88-1b9c-435c-85ba-c2e345b9f891
                    [senderOrderId] => 1234567890
                    [cargoes] => Array
                        (
                            [0] => Array
                                (
                                    [cargoId] => 048e260e-b0a9-42dd-974e-ebe5d72e0ace
                                    [senderCargoId] => 11300000294
                                    [barcode] => 32270000000001
                                )

                        )

                    [alreadyCreated] =>
                )

        )
        **/
        
        /** Ответ при ошибке
        Array
        (
            [success] =>
            [errorCode] => 400
            [errorMsg] => Element partnerOrder[0].shipmentDate has incorrect value
        )
        **/

    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```

<a name="cancel-order"><h1>Отмена заказа</h1></a>  
// TODO описание


<a name="orders-status"><h1>Статусы заказов</h1></a> 
Метож **getOrdersStatus** получает последний статус по списку заказов.  
Заказ можно запросить по своему уникальному номеру или по номеру в системе 5post. 
 
**Входные параметры:**
- *array[]* - массив массивов с номерами заказов.

**Выходные параметры:**
- *array* - последний статус заказов

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        // По ID заказа в системе клиента
        $result = $Client->getOrdersStatusByOrderId(['1234567891']);

        // По ID заказа в системе 5post
        $result = $Client->getOrdersStatusByVendorId(['12854f88-1b9c-435c-85ba-c2e345b9f891']);
        
        foreach ($result as $status) {
            // Проверка на конечный статусы
            if (\LapayGroup\FivePostSdk\Enum\OrderStatus::isFinal($status['executionStatus'])) {
                // TODO  логика обработки конечного статуса, после которого запрос статусов не требуется
            }
    
            // Получение текстового описания статуса
            $status_text   = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['status']);
            $exstatus_text = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['executionStatus']);
        }

        /** Пример ответа 1
        Array
        (
            [0] => Array
                (
                    [orderId] => c1ba069d-a1aa-49ae-a562-3dca429823f4
                    [senderOrderId] => 1234567891
                    [status] => NEW
                    [changeDate] => 2020-08-10T13:12:42.414673+03:00
                    [executionStatus] => CREATED
                )

        )

        Пример ответа 2
        Array
        (
            [0] => Array
                (
                    [status] => REJECTED
                    [orderId] => c1ba069d-a1aa-49ae-a562-3dca429823f4
                    [senderOrderId] => 1234567891
                    [executionStatus] => REJECTED: Ошибка валидации по плановым ВГХ
                    [changeDate] => 2020-08-10T13:12:43.31964+03:00
                )

        )
    
        Пример ответа 3
        Array
        (
            [0] => Array
                (
                    [status] => REJECTED
                    [orderId] => 12854f88-1b9c-435c-85ba-c2e345b9f891
                    [senderOrderId] => 1234567890
                    [executionStatus] => REJECTED: PickupPointDto is null with id = 13e9d62d-1799-4e14-a27b-d218f33de7f6
                    [changeDate] => 2020-08-10T12:39:12.933568+03:00
                )

        )
        **/
    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```

<a name="order-statuses"><h1>История статусов заказа</h1></a>  
Метод **getOrderStatuses** возвращает полную историю статусов заказа.

**Входные параметры:**
- *string|null $order_id* - ID заказа;
- *string|null $vendor_id* - ID заказа в системе 5post.

**Выходные параметры:**
- *array* - история статусов заказа

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        
        // По ID заказа в системе клиента
        $result = $Client->getOrderStatusesByOrderId('1234567891');

        // По ID заказа в системе 5post
        $result = $Client->getOrderStatusesByVendorId('12854f88-1b9c-435c-85ba-c2e345b9f891');
        
        foreach ($result as $status) {
            // Проверка на конечный статусы
            if (\LapayGroup\FivePostSdk\Enum\OrderStatus::isFinal($status['executionStatus'])) {
                // TODO  логика обработки конечного статуса, после которого запрос статусов не требуется
            }
    
            // Получение текстового описания статуса
            $status_text   = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['status']);
            $exstatus_text = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['executionStatus']);
        }

        /** 
        Array
        (
            [0] => Array
                (
                    [orderId] => c1ba069d-a1aa-49ae-a562-3dca429823f4
                    [senderOrderId] => 1234567891
                    [status] => REJECTED
                    [changeDate] => 2020-08-10T13:12:43.31964+03:00
                    [executionStatus] => REJECTED: PICKUP_POINT_SIZE_VALIDATION
                )
    
            [1] => Array
                (
                    [orderId] => c1ba069d-a1aa-49ae-a562-3dca429823f4
                    [senderOrderId] => 1234567891
                    [status] => REJECTED
                    [changeDate] => 2020-08-10T13:12:43.31447+03:00
                    [executionStatus] => CREATED: PICKUP_POINT_SIZE_VALIDATION
                )
    
            [2] => Array
                (
                    [orderId] => c1ba069d-a1aa-49ae-a562-3dca429823f4
                    [senderOrderId] => 1234567891
                    [status] => NEW
                    [changeDate] => 2020-08-10T13:12:42.414673+03:00
                    [executionStatus] => CREATED
                )
    
        )
        **/
        
        // По списку ID заказа в системе 5post
        $result = $Client->getOrderStatusesByListVendorId(['e04be1eb-6ebf-47a6-9069-4b41befd69ec', '951db785-3044-4f74-b008-fafa94c2658c']);
        
        // По списку ID заказа в системе клиента
        $result = $Client->getOrderStatusesByListOrderIds(['2207773', '2207765']);
        
        foreach ($result as $i => $order) {
            foreach ($order['statuses'] as $status) {
                // Проверка на конечный статусы
                if (\LapayGroup\FivePostSdk\Enum\OrderStatus::isFinal($status['executionStatus'])) {
                    // TODO  логика обработки конечного статуса, после которого запрос статусов не требуется
                }
        
                // Получение текстового описания статуса
                $status_text   = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['status']);
                $exstatus_text = \LapayGroup\FivePostSdk\Enum\OrderStatus::getNameByCode($status['executionStatus']);
            }
        }
        
        /**
        * Array
        (
            [0] => Array
                (
                    [orderId] => 951db785-3044-4f74-b008-fafa94c2658c
                    [senderOrderId] => 2207773
                    [statuses] => Array
                        (
                            [0] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:14.788767+03:00
                                    [status] => NEW
                                    [executionStatus] => APPROVED
                                )
        
                            [1] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:13.795966+03:00
                                    [status] => NEW
                                    [executionStatus] => CREATED
                                )
        
                            [2] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:15.917899+03:00
                                    [status] => APPROVED
                                    [executionStatus] => APPROVED
                                )
        
                        )
        
                )
        
            [1] => Array
                (
                    [orderId] => e04be1eb-6ebf-47a6-9069-4b41befd69ec
                    [senderOrderId] => 2207765
                    [statuses] => Array
                        (
                            [0] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:14.516638+03:00
                                    [status] => NEW
                                    [executionStatus] => CREATED
                                )
        
                            [1] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:16.530297+03:00
                                    [status] => NEW
                                    [executionStatus] => APPROVED
                                )
        
                            [2] => Array
                                (
                                    [changeDate] => 2021-12-13T14:54:17.846835+03:00
                                    [status] => APPROVED
                                    [executionStatus] => APPROVED
                                )
        
                        )
        
                )
        
        )
        */
    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```


<a name="receipts"><h1>Получение чеков</h1></a>  
Метод **getReceiptsByListOrderIds** возвращает данные чеков по списку заказов в системе клиента.   
Метод **getReceiptsByListVendorIds** возвращает данные чеков по списку заказов в системе 5post.

**Входные параметры:**
- *string[] $order_ids* - Список id заказов клиента или 5post;

**Выходные параметры:**
- *array* - Список данных по чекам

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        
        // По списку ID заказов в системе клиента
        $result = $Client->getReceiptsByListOrderIds(['1234567891']);

        // По списку ID заказов в системе 5post
        $result = $Client->getReceiptsByListVendorIds(['55c9c98f-0b0d-4dbc-af9e-20cffa31eeaa']);
        
        /** 
        Array (
            [0] => Array (
                [orderId] => 55c9c98f-0b0d-4dbc-af9e-20cffa31eeaa
                [senderOrderId] => 2229829
                [clientOrderId] => 2218386
                [receipts] => Array
                    (
                        [0] => Array
                            (
                                [receiptDate] => 2022-03-25T11:43:15.032252+03:00
                                [receiptAmount] => 2140
                                [receiptType] => FULL_PAYMENT
                                [fiscalNumber] => 9960440300516976
                                [fiscalDocument] => 2959
                                [fiscalAttribute] => 363421806
                                [receiptUrl] => https://ofd.beeline.ru/check-order?fn={FN}&fp={FP}&fd={FD}
                                [payments] => Array
                                    (
                                        [0] => Array
                                            (
                                                [amount] => 2140
                                                [type] => ITEM_PAYMENT
                                                [method] => CARD
                                            )
    
                                    )
    
                            )
    
                    )
            )
        */
    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```


<a name="order-labels"><h1>Получение этикетки</h1></a>  
Методы работают только с заказами, который созданы через метод API V3.    
Метод **getReceiptsByListOrderIds** возвращает данные чеков по списку заказов в системе клиента.   
Метод **getReceiptsByListVendorIds** возвращает данные чеков по списку заказов в системе 5post.

**Входные параметры:**
- *string[] $order_ids* - Список id заказов клиента или 5post;
- *string $format* - Формат этикетки. Допустимые значения PDF и ZIP (опциональный параметр);  

**Выходные параметры:**
- *UploadedFile* - Файл с этикетками в формате ZIP или PDF.

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        
        // По ID заказа в системе клиента
        $result = $Client->getLabelsByListOrderIds(['1234567891']);

        // По ID заказа в системе 5post
        $result = $Client->getLabelsByListVendorIds(['12854f88-1b9c-435c-85ba-c2e345b9f891']);
    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```


<a name="np-zero"><h1>Обнуление наложенного платежа</h1></a>   
Метод **getReceiptsByListOrderIds** возвращает данные чеков по списку заказов в системе клиента.   
Метод **getReceiptsByListVendorIds** возвращает данные чеков по списку заказов в системе 5post.    

**Входные параметры:**
- *string[] $order_ids* - Список id заказов клиента или 5post;

**Выходные параметры:**
- *array* - Список данных по результату обнуления НП.

**Примеры вызова:**
```php
<?php
    try {
        $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_TEST);
        
        // По ID заказа в системе клиента
        $result = $Client->removePaymentByListOrderIds(['1234567891']);

        // По ID заказа в системе 5post
        $result = $Client->removePaymentByListVendorIds(['12854f88-1b9c-435c-85ba-c2e345b9f891']);
        
        /** 
        Array
        (
            [0] => Array
            (    
                [orderId] => 12854f88-1b9c-435c-85ba-c2e345b9f891
                [senderOrderId] => 1234567891
                [success] => true
            )
        )
        */
    }
     
    catch (LapayGroup\FivePostSdk\Exceptions\FivePostException $e) {
        // Обработка ошибки вызова API 5post
        // $e->getMessage(); // текст ошибки 
        // $e->getCode(); // http код ответа сервиса 5post или код ошибки при наличии узла error в ответе
        // $e->getRawResponse(); // ответ сервера 5post как есть (http request body)
    }  
 
    catch (\Exception $e) {
        // Обработка исключения
    }
```
