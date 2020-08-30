<?php

namespace LapayGroup\FivePostSdk\Helpers;

interface JwtSaveInterface
{
    /**
     * Чтение JWT из хранилища
     * @return string|null - JWT из хранилища
     */
    public function getToken();

    /**
     * Запись JWT в хранилище
     * @param string $token - JWT
     * @return string
     */
    public function setToken($token);
}