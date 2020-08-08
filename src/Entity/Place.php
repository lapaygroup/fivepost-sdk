<?php
namespace LapayGroup\FivePostSdk\Entity;

class Place
{
    /** @var string|null  */
    private $id = null; // Идентификатор груза в системе отправителя. Рекомендуем ставить значение совпадающее с ШК места

    /** @var null  */
    private $barcode = null; // ШК места

    /** @var float|null  */
    private $price = null; // Оценочная стоимость груза  включая НДС)

    /** @var string  */
    private $currency = 'RUB'; // Код валюты оценочной стоимости

    /** @var int  */
    private $height = 0; // Высота груза, мм

    /** @var int  */
    private $length = 0; // Длина груза, мм

    /** @var int  */
    private $width = 0; // Ширина груза, мм

    /** @var int  */
    private $weight = 0; // Вес груза, мг

    /** @var int|null  */
    private $vat_rate = null; // Ставка НДС груза

    /** @var array  */
    private $items = []; // Вложения

    /**
     * Формирует массив параметров для запроса
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function asArr()
    {
        $params = [];

        foreach (['id', 'barcode', 'currency', 'price', 'currency', 'height', 'length', 'width', 'weight', 'vat_rate'] as $property) {
            if (is_null($this->$property))
                throw new \InvalidArgumentException('У места не заполнено обязательное поле '.$property);
        }

        $params['senderCargoId'] = $this->id;
        $params['barcodes'][0]['value'] = $this->barcode;
        $params['currency'] = $this->currency;
        $params['price'] = $this->price;
        $params['height'] = $this->height;
        $params['length'] = $this->length;
        $params['width'] = $this->weight;
        $params['weight'] = $this->weight;
        $params['vat'] = $this->vat_rate;

        // Формирование вложений места
        $params['productValues'] = [];

        /** @var Item $item */
        foreach ($this->items as $key => $item) {
            $params['productValues'][] = $item->asArr();
        }

        return $params;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     * @param null $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
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
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
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
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Item $item
     */
    public function setItem($item)
    {
        $this->items[] = $item;
    }
}