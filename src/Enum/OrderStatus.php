<?php

namespace LapayGroup\FivePostSdk\Enum;

class OrderStatus
{
    const S_NEW = 'NEW';
    const APPROVED = 'APPROVED';
    const REJECTED = 'REJECTED';
    const IN_PROCESS = 'IN_PROCESS';
    const DONE = 'DONE';
    const INTERRUPTED = 'INTERRUPTED';
    const CANCELLED = 'CANCELLED';
    const UNCLAIMED = 'UNCLAIMED';
    const CREATED = 'CREATED';
    const PROBLEM = 'PROBLEM';
    const RECEIVED_IN_WAREHOUSE_BY_PLACES = 'RECEIVED_IN_WAREHOUSE_BY_PLACES';
    const PRESORTED = 'PRESORTED';
    const RECEIVED_IN_WAREHOUSE_IN_DETAILS = 'RECEIVED_IN_WAREHOUSE_IN_DETAILS';
    const SORTED_IN_WAREHOUSE = 'SORTED_IN_WAREHOUSE';
    const PLACED_IN_CONSOLIDATION_CELL_IN_WAREHOUSE = 'PLACED_IN_CONSOLIDATION_CELL_IN_WAREHOUSE';
    const COMPLECTED_IN_WAREHOUSE = 'COMPLECTED_IN_WAREHOUSE';
    const READY_TO_BE_SHIPPED_FROM_WAREHOUSE = 'READY_TO_BE_SHIPPED_FROM_WAREHOUSE';
    const PLACED_IN_POSTAMAT = 'PLACED_IN_POSTAMAT';
    const PICKED_UP = 'PICKED_UP';
    const READY_FOR_WITHDRAW_FROM_PICKUP_POINT = 'READY_FOR_WITHDRAW_FROM_PICKUP_POINT';
    const WITHDRAWN_FROM_PICKUP_POINT = 'WITHDRAWN_FROM_PICKUP_POINT';
    const WAITING_FOR_REPICKUP = 'WAITING_FOR_REPICKUP';
    const LOST = 'LOST';
    const UTILIZED = 'UTILIZED';
    const RETURNED_TO_PARTNER = 'RETURNED_TO_PARTNER';

    static $main_status_list = [
        'NEW'         => 'Новый',
        'APPROVED'    => 'Подтвержден',
        'REJECTED'    => 'Отклонен',
        'IN_PROCESS'  => 'В процессе исполнения',
        'DONE'        => 'Исполнен',
        'INTERRUPTED' => 'Исполнение прервано',
        'CANCELLED'   => 'Отменен',
        'UNCLAIMED'   => 'Не востребован'
    ];

    static $status_list = [
        'CREATED' => 'Создан',
        'APPROVED' => 'Подтвержден',
        'REJECTED' => 'Отклонен',
        'PROBLEM' => 'Проблема',
        'RECEIVED_IN_WAREHOUSE_BY_PLACES' => 'Принят на складе по грузоместам',
        'PRESORTED' => 'Предсортировка',
        'RECEIVED_IN_WAREHOUSE_IN_DETAILS' => 'Принят на складе детально',
        'SORTED_IN_WAREHOUSE' => 'На сортировке на складе',
        'PLACED_IN_CONSOLIDATION_CELL_IN_WAREHOUSE' => 'Размещен в ячейку консолидации',
        'COMPLECTED_IN_WAREHOUSE' => 'Скомплектован на складе',
        'READY_TO_BE_SHIPPED_FROM_WAREHOUSE' => 'Готов к отгрузке со склада',
        'PLACED_IN_POSTAMAT' => 'Размещен в постамате',
        'PICKED_UP' => 'Выдан',
        'READY_FOR_WITHDRAW_FROM_PICKUP_POINT' => 'Готов к изъятию из пункта выдачи',
        'WITHDRAWN_FROM_PICKUP_POINT' => 'Изъят из пункта выдачи',
        'WAITING_FOR_REPICKUP' => 'Ожидает повторную выдачу',
        'LOST' => 'Утерян',
        'UTILIZED' => 'Утилизирован',
        'CANCELLED' => 'Отменен',
        'RETURNED_TO_PARTNER' => 'Отгружен партнеру'
    ];

    /**
     * Возвращает текстовое описание статуса по его коду
     *
     * @param string $code - код статуса 5post
     * @return string - текстовое описание статуса
     */
    static public function getNameByCode($code)
    {
        if (empty(self::$main_status_list[$code]) && empty(self::$status_list[$code]))
            throw new \InvalidArgumentException('Передан не существующий код статуса заказа 5post');

        if (!empty(self::$main_status_list[$code])) return self::$main_status_list[$code];

        if (!empty(self::$status_list[$code])) return self::$status_list[$code];
    }


    /**
     * Возвращает признак конечный статус или нет
     *
     * @param string $code - код статуса исполнения заказа (executionStatus)
     * @return bool
     */
    static public function isFinal($code)
    {
        if (in_array($code, ['PICKED_UP', 'UTILIZED', 'CANCELLED', 'RETURNED_TO_PARTNER'])) {
            return true;
        } else {
            return false;
        }
    }
}