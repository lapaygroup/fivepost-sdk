<?php

namespace LapayGroup\FivePostSdk;

use LapayGroup\FivePostSdk\Exceptions\TokenException;

class Jwt
{
    public static $leeway = 0;
    public static $timestamp = null;

    public static $supported_algs = array(
        'ES256' => array('openssl', 'SHA256'),
        'HS256' => array('hash_hmac', 'SHA256'),
        'HS384' => array('hash_hmac', 'SHA384'),
        'HS512' => array('hash_hmac', 'SHA512'),
        'RS256' => array('openssl', 'SHA256'),
        'RS384' => array('openssl', 'SHA384'),
        'RS512' => array('openssl', 'SHA512'),
    );


    public static function decode($jwt, $allowed_algs = ['RS256'])
    {
        $timestamp = \is_null(static::$timestamp) ? \time() : static::$timestamp;

        $tks = \explode('.', $jwt);
        if (\count($tks) != 3) {
            throw new TokenException('Не верное количество сегментов в токене');
        }
        list($headb64, $bodyb64, $cryptob64) = $tks;
        if (null === ($header = static::jsonDecode(static::urlsafeB64Decode($headb64)))) {
            throw new TokenException('Ошибка получения заголовка токена');
        }
        if (null === $payload = static::jsonDecode(static::urlsafeB64Decode($bodyb64))) {
            throw new TokenException('Ошибка получения полезной нагрузки в токене');
        }
        if (false === ($sig = static::urlsafeB64Decode($cryptob64))) {
            throw new TokenException('Ошибка получения подписи в токене');
        }
        if (empty($header->alg)) {
            throw new TokenException('В токене не задан алгоритм');
        }
        if (empty(static::$supported_algs[$header->alg])) {
            throw new TokenException('Алгоритм не поддерживается');
        }
        if (!\in_array($header->alg, $allowed_algs)) {
            throw new TokenException('Недопустимый алгоритм');
        }

        if (isset($payload->nbf) && $payload->nbf > ($timestamp + static::$leeway)) {
            throw new TokenException(
                'Нельзя использовать токен до ' . \date(\DateTime::ISO8601, $payload->nbf)
            );
        }

        if (isset($payload->iat) && $payload->iat > ($timestamp + static::$leeway)) {
            throw new TokenException(
                'Нельзя использовать токен до ' . \date(\DateTime::ISO8601, $payload->iat)
            );
        }

        if (isset($payload->exp) && ($timestamp - static::$leeway) >= $payload->exp) {
            throw new TokenException('Истек срок действия токена');
        }

        return $payload;
    }

    /**
     * Раскодирование JSON строки в PHP объект
     *
     * @param string $input - раскодируемая JSON строка
     * @return object
     * @throws TokenException
     */
    public static function jsonDecode($input)
    {
        if (\version_compare(PHP_VERSION, '5.4.0', '>=') && !(\defined('JSON_C_VERSION') && PHP_INT_SIZE > 4)) {
            $obj = \json_decode($input, false, 512, JSON_BIGINT_AS_STRING);
        } else {
            $max_int_length = \strlen((string) PHP_INT_MAX) - 1;
            $json_without_bigints = \preg_replace('/:\s*(-?\d{'.$max_int_length.',})/', ': "$1"', $input);
            $obj = \json_decode($json_without_bigints);
        }

        if ($errno = \json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($obj === null && $input !== 'null') {
            throw new TokenException('Передана пустая строка для преобразования из JSON');
        }
        return $obj;
    }

    /**
     * Кодирование PHP объекта в JSON строку
     *
     * @param object|array $input - объект
     * @return string
     * @throws TokenException
     */
    public static function jsonEncode($input)
    {
        $json = \json_encode($input);
        if ($errno = \json_last_error()) {
            static::handleJsonError($errno);
        } elseif ($json === 'null' && $input !== null) {
            throw new TokenException('Передан пустой объект для преобразования в JSON');
        }
        return $json;
    }

    /**
     * Раскодирование строки URL-safe Base64
     *
     * @param string $input - Base64 строка
     * @return string
     */
    public static function urlsafeB64Decode($input)
    {
        $remainder = \strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= \str_repeat('=', $padlen);
        }
        return \base64_decode(\strtr($input, '-_', '+/'));
    }

    /**
     * Кодирование в URL-safe Base64
     *
     * @param string $input - кодируемая строка
     * @return string
     */
    public static function urlsafeB64Encode($input)
    {
        return \str_replace('=', '', \strtr(\base64_encode($input), '+/', '-_'));
    }

    /**
     * Генерация JSON ошибки
     *
     * @param int $errno Номер ошибки из json_last_error()
     * @return void
     */
    private static function handleJsonError($errno)
    {
        $messages = array(
            JSON_ERROR_DEPTH => 'Достигнута максимальная глубина стека',
            JSON_ERROR_STATE_MISMATCH => 'Неверный или некорректный JSON',
            JSON_ERROR_CTRL_CHAR => 'Ошибка управляющего символа, возможно неверная кодировка',
            JSON_ERROR_SYNTAX => 'Синтаксическая ошибка',
            JSON_ERROR_UTF8 => 'Некорректные символы UTF-8, возможно неверная кодировка'
        );
        throw new TokenException(
            isset($messages[$errno])
                ? $messages[$errno]
                : 'Unknown JSON error: ' . $errno
        );
    }
}