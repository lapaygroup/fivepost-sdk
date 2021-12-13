<?php
namespace LapayGroup\FivePostSdk\Entity;

class Item
{
    /** @var string|null  */
    private $barcode = null; // ШК товара

    /** @var string|null  */
    private $code_gtg = null; // Номер Грузовой Таможенной Декларации

    /** @var string|null  */
    private $code_tnved = null; // Код Товарной Номенклатуры Внешне Экономической Деятельности

    /** @var string|null  */
    private $origin_country = null; // Страна производства

    /** @var string|null  */
    private $upi_code = null; // Код маркировки товара согласно Честному Знаку.  Принимаемый формат значений только base64.

    /** @var string|null  */
    private $name = null; // Название товара

    /** @var float|null  */
    private $price = null; // Цена за единицу товара (включая НДС)

    /** @var string|null  */
    private $currency = null; // Код валюты цены товара Alpha-3

    /** @var int|null  */
    private $vat_rate = null; // Ставка НДС в %. Возможные значения: 0 (=без НДС), 10, 20

    /** @var int  */
    private $quantity = 1; // Количество товаров этого наименования в грузе

    /** @var string|null  */
    private $articul = null; // Артикул товара

    /**
     * Формирует массив параметров для запроса
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function asArr()
    {
        $params = [];

        foreach (['name', 'price', 'quantity', 'vat_rate'] as $property) {
            if (is_null($this->$property))
                throw new \InvalidArgumentException('В товаре не заполнено обязательное поле '.$property);
        }

        if (!empty($this->barcode))
            $params['barcode'] = $this->barcode;

        if (!empty($this->code_gtg))
            $params['codeGTD'] = $this->code_gtg;

        if (!empty($this->code_tnved))
            $params['codeTNVED'] = $this->code_tnved;

        if (!empty($this->code_tnved))
            $params['currency'] = $this->currency;

        if (!empty($this->origin_country))
            $params['originCountry'] = $this->origin_country;

        if (!empty($this->articul))
            $params['vendorCode'] = $this->articul;

        if (!empty($this->upi_code))
            $params['productValues'] = $this->upi_code;

        $params['name'] = $this->name;
        $params['price'] = $this->price;
        $params['value'] = $this->quantity;
        $params['vat'] = $this->vat_rate;

        return $params;
    }

    /**
     * @return string|null
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param string|null $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     * @return string|null
     */
    public function getCodeGtg()
    {
        return $this->code_gtg;
    }

    /**
     * @param string|null $code_gtg
     */
    public function setCodeGtg($code_gtg)
    {
        $this->code_gtg = $code_gtg;
    }

    /**
     * @return string|null
     */
    public function getCodeTnved()
    {
        return $this->code_tnved;
    }

    /**
     * @param string|null $code_tnved
     */
    public function setCodeTnved($code_tnved)
    {
        $this->code_tnved = $code_tnved;
    }

    /**
     * @return string|null
     */
    public function getOriginCountry()
    {
        return $this->origin_country;
    }

    /**
     * @param string|null $origin_country
     */
    public function setOriginCountry($origin_country)
    {
        $this->origin_country = $origin_country;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return float|null
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float|null $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return int|null
     */
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * @param int|null $vat_rate
     */
    public function setVatRate($vat_rate)
    {
        $this->vat_rate = $vat_rate;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string|null
     */
    public function getArticul()
    {
        return $this->articul;
    }

    /**
     * @param string|null $articul
     */
    public function setArticul($articul)
    {
        $this->articul = $articul;
    }
}