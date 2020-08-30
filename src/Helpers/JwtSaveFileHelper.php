<?php

namespace LapayGroup\FivePostSdk\Helpers;

use LapayGroup\FivePostSdk\Helpers\JwtSaveInterface;

class JwtSaveFileHelper implements JwtSaveInterface
{
    private $tmp_file = '/tmp/5postjwt';

    public function __construct()
    {
        $this->createTmpFile();
    }

    private function createTmpFile()
    {
        if (!file_exists($this->tmp_file)) {
            file_put_contents($this->tmp_file, '');
        }
    }

    /**
     * Чтение JWT токена из временного файла
     *
     * @return string|null
     */
    public function getToken()
    {
        $this->createTmpFile();
        return file_get_contents($this->tmp_file);
    }

    /**
     * Запись JWT в временный файл
     *
     * @param string $token
     * @return void
     */
    public function setToken($token)
    {
        file_put_contents($this->tmp_file, $token);
    }

    /**
     * @return string
     */
    public function getTmpFile()
    {
        return $this->tmp_file;
    }

    /**
     * @param string $tmp_file
     */
    public function setTmpFile($tmp_file)
    {
        $this->tmp_file = $tmp_file;
    }
}