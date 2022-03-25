<?php
namespace LapayGroup\FivePostSdk;

use LapayGroup\FivePostSdk\Entity\Order;

trait TariffsTrait {
    private $return_percent          = 0.50;  // Тариф за возврат невыкупленных отправлений и обработку и возврат отмененных отправлений
    private $valuated_amount_percent = 0.005;  // Сбор за объявленную ценность
    private $cash_percent            = 0.0192; // Вознаграждение за прием наложенного платежа наличными
    private $card_percent            = 0.0264; // Вознаграждение за прием платежа с использованием банковских карт

    private $zone_tariffs = [
        '1' => [
            'basic_price' => 129,
            'overload_kg_price' => 18,
            'delivery_days' => 3
        ],
        '2' => [
            'basic_price' => 138,
            'overload_kg_price' => 18,
            'delivery_days' => 3
        ],
        '3' => [
            'basic_price' => 148,
            'overload_kg_price' => 18,
            'delivery_days' => 3
        ],
        '4' => [
            'basic_price' => 160,
            'overload_kg_price' => 36,
            'delivery_days' => 6
        ],
        '5' => [
            'basic_price' => 205,
            'overload_kg_price' => 36,
            'delivery_days' => 6
        ],
        '6' => [
            'basic_price' => 180,
            'overload_kg_price' => 36,
            'delivery_days' => 6
        ],
        '7' => [
            'basic_price' => 225,
            'overload_kg_price' => 36,
            'delivery_days' => 6
        ],
        '8' => [
            'basic_price' => 217,
            'overload_kg_price' => 54,
            'delivery_days' => 9
        ],
        '9' => [
            'basic_price' => 259,
            'overload_kg_price' => 54,
            'delivery_days' => 10
        ],
        '10' => [
            'basic_price' => 268,
            'overload_kg_price' => 54,
            'delivery_days' => 8
        ],
        '11' => [
            'basic_price' => 290,
            'overload_kg_price' => 54,
            'delivery_days' => 8
        ],
        '12' => [
            'basic_price' => 348,
            'overload_kg_price' => 54,
            'delivery_days' => 8
        ],
        '13' => [
            'basic_price' => 368,
            'overload_kg_price' => 72,
            'delivery_days' => 9
        ]
    ];

    /**
     * Расчет стоимости доставки
     *
     * @param string $zone - Тарифная зона
     * @param int $weight - Вес заказа в граммах
     * @param float $amount - Стоимость заказа
     * @param string $payment_type - Тип оплаты (оплачен, картой или наличными)
     * @param bool $returned - Возврат в случае невыкупа
     * @return array - Стоимость доставки с сроком доставки
     */
    public function calculationTariff($zone = '1', $weight = 100, $amount = 0, $payment_type = Order::P_TYPE_PREPAYMENT, $returned = false)
    {
        if (empty($zone))
            throw new \InvalidArgumentException('Не передана тарифная зона');

        if (empty($weight))
            throw new \InvalidArgumentException('Не передан вес заказа');

        if (empty($this->zone_tariffs[$zone]))
            throw new \InvalidArgumentException('Тарифная зона не найдена');

        if (!empty($amount) && $payment_type == Order::P_TYPE_PREPAYMENT)
            throw new \InvalidArgumentException('Передан не корректный тип оплаты');

        $weight /= 1000;

        if ($weight > 3) {
            $tariff = $this->zone_tariffs[$zone]['basic_price'] + (($weight - 3) * $this->zone_tariffs[$zone]['overload_kg_price']);
        } else {
            $tariff = $this->zone_tariffs[$zone]['basic_price'];
        }

        if ($returned)
            $tariff += $tariff * $this->return_percent;

        if ($amount > 0) {
            $tariff += $amount * $this->valuated_amount_percent;
        }

        if ($payment_type == Order::P_TYPE_CASHLESS)
            $tariff += $amount * $this->card_percent;

        if ($payment_type == Order::P_TYPE_CASH)
            $tariff += $amount * $this->cash_percent;

        return ['price' => round($tariff, 2), 'delivery_days' => $this->zone_tariffs[$zone]['delivery_days']];
    }

    /**
     * @return int
     */
    public function getReturnPercent()
    {
        return $this->return_percent;
    }

    /**
     * @param int $return_percent
     */
    public function setReturnPercent($return_percent)
    {
        $this->return_percent = $return_percent;
    }

    /**
     * @return float
     */
    public function getValuatedAmountPercent()
    {
        return $this->valuated_amount_percent;
    }

    /**
     * @param float $valuated_amount_percent
     */
    public function setValuatedAmountPercent($valuated_amount_percent)
    {
        $this->valuated_amount_percent = $valuated_amount_percent;
    }

    /**
     * @return float
     */
    public function getCashPercent()
    {
        return $this->cash_percent;
    }

    /**
     * @param float $cash_percent
     */
    public function setCashPercent($cash_percent)
    {
        $this->cash_percent = $cash_percent;
    }

    /**
     * @return float
     */
    public function getCardPercent()
    {
        return $this->card_percent;
    }

    /**
     * @param float $card_percent
     */
    public function setCardPercent($card_percent)
    {
        $this->card_percent = $card_percent;
    }

    /**
     * @return array
     */
    public function getZoneTariffs()
    {
        return $this->zone_tariffs;
    }

    /**
     * @param array $zone_tariffs
     */
    public function setZoneTariffs($zone_tariffs)
    {
        $this->zone_tariffs = $zone_tariffs;
    }
}