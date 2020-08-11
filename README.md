
<a href="https://lapay.group/"><img align="left" width="200" src="https://lapay.group/lglogo.jpg"></a>
<a href="http://fivepost.ru"><img align="right" width="200" src="https://lapay.group/fivepostlogo.png"></a>    

<br /><br /><br />

[![Latest Stable Version](https://poser.pugx.org/lapaygroup/fivepost-sdk/v/stable)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![Total Downloads](https://poser.pugx.org/lapaygroup/fivepost-sdk/downloads)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![License](https://poser.pugx.org/lapaygroup/fivepost-sdk/license)](https://packagist.org/packages/lapaygroup/fivepost-sdk)
[![Telegram Chat](https://img.shields.io/badge/telegram-chat-blue.svg?logo=telegram)](https://t.me/phpboxberrysdk)

# SDK для [интеграции с программным комплексом 5post](http://fivepost.ru).  

Посмотреть все проекты или подарить автору кофе можно [тут](https://lapay.group/opensource).    

# Содержание    
- [Changelog](#changelog)    
- [Конфигурация](#configuration)  
- [Отладка](#debugging)  


<a name="links"><h1>Changelog</h1></a>  
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

```php
try {
    // Инициализация API клиента по api-key с таймаутом ожидания ответа 60 секунд
    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_PROD);
    $jwt = $Client->getJwt(); // $jwt = eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJPcGVuQVBJIiwiYXVkIjoiQTEyMjAxOSEiLCJhcGlrZXkiOiJBSlMxU0lTRHJrNmRyMFpYazVsZVQxdFBGZDRvcXNIYSIsImlzcyI6InVybjovL0FwaWdlZSIsInBhcnRuZXJJZCI6ImIyNzNlYzQ0LThiMDAtNDliMS04OWVlLWQ4Njc5NjMwZDk0OCIsImV4cCI6MTU5NzA4OTk1OCwiaWF0IjoxNTk3MDg2MzU4LCJqdGkiOiI4YTIyZmUzNy1mMzc0LTQ0NDctOGMzMC05N2ZiYjJjOGQ3MTkifQ.G_XQ6vdk7bXfIeMJer7z5WUFqnwlp0qUt6RxaCINZt3b97ZUwPMI1-1FNKQhFwmCHJGpTYyBJKHgtY3uJZOWDAszjPMIHrQrcnJLSzJisNiy6z3cMbpf-UgD-RgebuaYyEgZ81rekL5aUN6r5rqWHbxcxEGY22lTy9uEWwxF_-UdVLEW9O9Z9M9IMlL5_7ACVu-ID2n6zFk_QJnEumJcBSqb6JFh2TWvUPnjnUt5AOiD7gNRXKsBvoC6InSfGoMA461cxu-rAazhNq5fkqFSdrIUyz0kvAb3UI4hs_6xJy9tXPpXIQY7LQUZqQGp5BT8pasfhAJ_4CCATbqxIHmY9w
    $result = \LapayGroup\FivePostSdk\Jwt::decode($jwt); // Получения информации из токена (payload)

    // Ранее полученный токен можно добавить в клиент специльным методом
    $Client->setJwt($jwt);
}

catch (\LapayGroup\FivePostSdk\Exception\FivePostException $e) {
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

    $Client = new LapayGroup\FivePostSdk\Client('api-key', 60, \LapayGroup\FivePostSdk\Client::API_URI_PROD);
    $Client->setLogger($log);
    $jwt = $Client->getJwt(); // $jwt = eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJzdWIiOiJPcGVuQVBJIiwiYXVkIjoiQTEyMjAxOSEiLCJhcGlrZXkiOiJBSlMxU0lTRHJrNmRyMFpYazVsZVQxdFBGZDRvcXNIYSIsImlzcyI6InVybjovL0FwaWdlZSIsInBhcnRuZXJJZCI6ImIyNzNlYzQ0LThiMDAtNDliMS04OWVlLWQ4Njc5NjMwZDk0OCIsImV4cCI6MTU5NzA4OTk1OCwiaWF0IjoxNTk3MDg2MzU4LCJqdGkiOiI4YTIyZmUzNy1mMzc0LTQ0NDctOGMzMC05N2ZiYjJjOGQ3MTkifQ.G_XQ6vdk7bXfIeMJer7z5WUFqnwlp0qUt6RxaCINZt3b97ZUwPMI1-1FNKQhFwmCHJGpTYyBJKHgtY3uJZOWDAszjPMIHrQrcnJLSzJisNiy6z3cMbpf-UgD-RgebuaYyEgZ81rekL5aUN6r5rqWHbxcxEGY22lTy9uEWwxF_-UdVLEW9O9Z9M9IMlL5_7ACVu-ID2n6zFk_QJnEumJcBSqb6JFh2TWvUPnjnUt5AOiD7gNRXKsBvoC6InSfGoMA461cxu-rAazhNq5fkqFSdrIUyz0kvAb3UI4hs_6xJy9tXPpXIQY7LQUZqQGp5BT8pasfhAJ_4CCATbqxIHmY9w
    $result = \LapayGroup\FivePostSdk\Jwt::decode($jwt);
```

В log.txt будут логи в виде:
```
[2020-08-10T10:19:15.236829+00:00] 5post-api.INFO: 5Post API POST request /api/v1/getOrderStatus: [{"senderOrderId":"1234567891"}] [] []
[2020-08-10T10:19:15.447289+00:00] 5post-api.INFO: 5Post API response /api/v1/getOrderStatus: [{"status":"REJECTED","orderId":"c1ba069d-a1aa-49ae-a562-3dca429823f4","senderOrderId":"1234567891","executionStatus":"REJECTED: Ошибка валидации по плановым ВГХ","changeDate":"2020-08-10T13:12:43.31964+03:00"}] {"Date":["Mon, 10 Aug 2020 10:19:15 GMT"],"Content-Type":["application/json"],"Connection":["keep-alive"],"Set-Cookie":["d46d09d93a8e8174c6300478f336d992=faa2eebdd2dfdd0ce10a284ccfcbdaea; path=/; HttpOnly; Secure","TS01ab71c3=01a93f75476aa0457856d9bf9d665b19c12ba5ec238a9a08d7fe077961dddc634278c540aed3dd2010a567571da4de02529e7117e9f863d3ac1f9716c0e5a25f8446c685c6; Path=/; Domain=.api-omni.x5.ru"],"Access-Control-Allow-Origin":[""],"Access-Control-Allow-Headers":["origin, x-requested-with, accept, content-type, authorization"],"Access-Control-Max-Age":["3628800"],"Access-Control-Allow-Credentials":["true"],"Access-Control-Allow-Methods":["GET, PUT, POST, DELETE"],"strict-transport-security":["max-age=31536000"],"x-frame-options":["SAMEORIGIN"],"Transfer-Encoding":["chunked"],"http_status":200} []

```