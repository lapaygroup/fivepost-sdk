<?php
namespace LapayGroup\FivePostSdk\Entity;

class WorkingDay
{
    /** @var int|null  */
    private $day = null; // Номер дня недели 1 - 7 (1 - понедельник)

    /** @var string|null  */
    private $time_from = null; // Время открытия (10:00:00)

    /** @var string|null  */
    private $time_till = null; // Время закрытия (22:00:00)

    public function asArr()
    {
        $params = [];
        $params['dayNumber'] = $this->day;
        $params['timeFrom'] = $this->time_from;
        $params['timeTill'] = $this->time_till;
    }

    /**
     * @return int|null
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param int|null $day
     */
    public function setDay($day)
    {
        $this->day = $day;
    }

    /**
     * @return string|null
     */
    public function getTimeFrom()
    {
        return $this->time_from;
    }

    /**
     * @param string|null $time_from
     */
    public function setTimeFrom($time_from)
    {
        $this->time_from = $time_from;
    }

    /**
     * @return string|null
     */
    public function getTimeTill()
    {
        return $this->time_till;
    }

    /**
     * @param string|null $time_till
     */
    public function setTimeTill($time_till)
    {
        $this->time_till = $time_till;
    }
}