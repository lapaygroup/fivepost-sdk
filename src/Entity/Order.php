<?php
namespace LapayGroup\FivePostSdk\Entity;

class Order
{
    const UNDELIVERED_RETURN = 'RETURN';
    const UNDELIVERED_UTIL   = 'UTILIZATION ';

    const P_TYPE_CASH = 'CASH';
    const P_TYPE_CASHLESS = 'CASHLESS';
    const P_TYPE_PREPAYMENT = 'PREPAYMENT';

    /** @var string|null */
    private $id = null; // Уникальный идентификатор заказа в системе отправителя

    /** @var string|null */
    private $number = null; // Номер заказа для информирования конечного получателя заказа

    /** @var string|null */
    private $company_name = null; // Бренд отправителя. Передаётся клиенту в смс, как отправитель заказа

    /** @var string|null  */
    private $fio = null; // ФИО получателя заказа

    /** @var string|null  */
    private $phone = null; // Телефон клиента-получателя в формате +79XXXXXXXXX, 79XXXXXXXXX, 89XXXXXXXXX и 9XXXXXXXXX

    /** @var string|null  */
    private $email = null; // E-mail получателя заказа

    /** @var string|null  */
    private $pvz_id = null; // UUID точки выдачи заказа

    /** @var string|null  */
    private $warehouse_id = null; // ID склада забора

    /** @var \DateTime|null  */
    private $planned_receive_date = null; // Плановая дата передачи заказа покупателю

    /** @var \DateTime|null  */
    private $created_date = null; // Дата создания заказа

    /** @var \DateTime|null  */
    private $shipment_date = null; // Плановая дата отгрузки заказа со склада

    /** @var string  */
    private $undeliverable_option = self::UNDELIVERED_RETURN; // Способ обработки невостребованных заказов

    /** @var float|null  */
    private $delivery_cost = null; // Стоимость доставки для клиента (включая НДС)

    /** @var string */
    private $delivery_cost_cur = null; // Код валюты для стоимости доставки Alpha-3 (https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%B4%D1%8B_%D0%B8_%D0%BA%D0%BB%D0%B0%D1%81%D1%81%D0%B8%D1%84%D0%B8%D0%BA%D0%B0%D1%82%D0%BE%D1%80%D1%8B_%D0%B2%D0%B0%D0%BB%D1%8E%D1%82)

    /** @var float|null  */
    private $payment_value = null; // Сумма к оплате

    /** @var string  */
    private $payment_cur = 'RUB'; // Код валюты оплаты Alpha-3

    /** @var string  */
    private $payment_type = self::P_TYPE_PREPAYMENT; // Способ оплаты (CASH – оплата наличными, CASHLESS – оплата картой, PREPAYMENT – предоплата)

    /** @var float|null  */
    private $price = null; // Оценочная стоимость заказа (включая НДС). Равняется сумме всех товаров.

    /** @var string  */
    private $price_cur = 'RUB'; // Код валюты оценочной стоимости Alpha-3

    /** @var array  */
    private $places = []; // Места в заказе


    /**
     * Формирует массив параметров для запроса
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    public function asArr()
    {
        $required_fields = [
            'id',
            'company_name',
            'number',
            'fio',
            'phone',
            'pvz_id',
            'warehouse_id',
            'undeliverable_option',
            'payment_value',
            'payment_cur',
            'payment_type',
            'price',
            'price_cur'
        ];

        foreach ($required_fields as $property) {
            if (is_null($this->$property))
                throw new \InvalidArgumentException('В заказе не заполнено обязательное поле '.$property);
        }

        $params = [];
        $params['senderOrderId'] = $this->id;
        $params['brandName'] = $this->company_name;
        $params['clientOrderId'] = $this->number;
        $params['clientName'] = $this->fio;
        $params['clientPhone'] = $this->phone;
        $params['receiverLocation'] = $this->pvz_id;
        $params['senderLocation'] = $this->warehouse_id;
        $params['undeliverableOption'] = $this->undeliverable_option;

        if (!empty($this->email))
            $params['clientEmail'] = $this->email;

        if (!empty($this->planned_receive_date)) {
            $params['plannedReceiveDate'] = $this->planned_receive_date
                    ->setTimezone((new \DateTimeZone('UTC')))
                    ->format('c') . 'Z';
        }

        if (!empty($this->created_date)) {
            $params['senderCreateDate'] = $this->created_date
                    ->setTimezone((new \DateTimeZone('UTC')))
                    ->format('c') . 'Z';
        }

        if (!empty($this->shipment_date)) {
            $params['shipmentDate'] = $this->shipment_date
                    ->setTimezone((new \DateTimeZone('UTC')))
                    ->format('c') . 'Z';
        }

        // cost block
        $params['cost']['paymentValue'] = $this->payment_value;
        $params['cost']['paymentCurrency'] = $this->payment_cur;
        $params['cost']['paymentType'] = $this->payment_type;
        $params['cost']['price'] = $this->price;
        $params['cost']['priceCurrency'] = $this->price_cur;

        if (empty($this->delivery_cost))
            $params['cost']['deliveryCost'] = $this->delivery_cost;

        if (empty($this->delivery_cost_cur))
            $params['cost']['deliveryCostCurrency'] = $this->delivery_cost_cur;

        // cargoes block
        $params['cargoes'] = [];
        /** @var Place $place */
        foreach ($this->places as $place) {
            $params['cargoes'][] = $place->asArr();
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
     * @return string|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string|null $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->company_name;
    }

    /**
     * @param string|null $company_name
     */
    public function setCompanyName($company_name)
    {
        $this->company_name = $company_name;
    }

    /**
     * @return string|null
     */
    public function getFio()
    {
        return $this->fio;
    }

    /**
     * @param string|null $fio
     */
    public function setFio($fio)
    {
        $this->fio = $fio;
    }

    /**
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getPvzId()
    {
        return $this->pvz_id;
    }

    /**
     * @param string|null $pvz_id
     */
    public function setPvzId($pvz_id)
    {
        $this->pvz_id = $pvz_id;
    }

    /**
     * @return string|null
     */
    public function getWarehouseId()
    {
        return $this->warehouse_id;
    }

    /**
     * @param string|null $warehouse_id
     */
    public function setWarehouseId($warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;
    }

    /**
     * @return \DateTime|null
     */
    public function getPlannedReceiveDate()
    {
        return $this->planned_receive_date;
    }

    /**
     * @param \DateTime|null $planned_receive_date
     */
    public function setPlannedReceiveDate($planned_receive_date)
    {
        $this->planned_receive_date = $planned_receive_date;
    }

    /**
     * @return \DateTime|null
     */
    public function getCreatedDate()
    {
        return $this->created_date;
    }

    /**
     * @param \DateTime|null $created_date
     */
    public function setCreatedDate($created_date)
    {
        $this->created_date = $created_date;
    }

    /**
     * @return \DateTime|null
     */
    public function getShipmentDate()
    {
        return $this->shipment_date;
    }

    /**
     * @param \DateTime|null $shipment_date
     */
    public function setShipmentDate($shipment_date)
    {
        $this->shipment_date = $shipment_date;
    }

    /**
     * @return string
     */
    public function getUndeliverableOption()
    {
        return $this->undeliverable_option;
    }

    /**
     * @param string $undeliverable_option
     */
    public function setUndeliverableOption($undeliverable_option)
    {
        $this->undeliverable_option = $undeliverable_option;
    }

    /**
     * @return float|null
     */
    public function getDeliveryCost()
    {
        return $this->delivery_cost;
    }

    /**
     * @param float|null $delivery_cost
     */
    public function setDeliveryCost($delivery_cost)
    {
        $this->delivery_cost = $delivery_cost;
    }

    /**
     * @return string
     */
    public function getDeliveryCostCur()
    {
        return $this->delivery_cost_cur;
    }

    /**
     * @param string $delivery_cost_cur
     */
    public function setDeliveryCostCur($delivery_cost_cur)
    {
        $this->delivery_cost_cur = $delivery_cost_cur;
    }

    /**
     * @return float|null
     */
    public function getPaymentValue()
    {
        return $this->payment_value;
    }

    /**
     * @param float|null $payment_value
     */
    public function setPaymentValue($payment_value)
    {
        $this->payment_value = $payment_value;
    }

    /**
     * @return string
     */
    public function getPaymentCur()
    {
        return $this->payment_cur;
    }

    /**
     * @param string $payment_cur
     */
    public function setPaymentCur($payment_cur)
    {
        $this->payment_cur = $payment_cur;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->payment_type;
    }

    /**
     * @param string $payment_type
     */
    public function setPaymentType($payment_type)
    {
        $this->payment_type = $payment_type;
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
    public function getPriceCur()
    {
        return $this->price_cur;
    }

    /**
     * @param string $price_cur
     */
    public function setPriceCur($price_cur)
    {
        $this->price_cur = $price_cur;
    }

    /**
     * @return array
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * @param Place $place
     */
    public function setPlace($place)
    {
        $this->places[] = $place;
    }
}
