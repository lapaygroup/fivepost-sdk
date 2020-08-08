<?php
namespace LapayGroup\FivePostSdk\Entity;

class Warehouse
{
    /** @var string|null  */
    private $id = null; // ID склада в системе клиента (используется при создании заказов в поле senderLocation)

    /** @var string|null  */
    private $name = null; // Наименование склада партнера (напр. Romashka-1)

    /** @var string|null  */
    private $country_id = 'RU'; // Двухбуквенный код страны (https://ru.wikipedia.org/wiki/ISO_3166-1 Alpha-2)

    /** @var int  */
    private $region_code = 77; // Цифровой код региона России

    /** @var string|null  */
    private $federal_district = null; // Наименование области

    /** @var string|null  */
    private $region = null; // Наименование региона

    /** @var string|null  */
    private $zip_code = null; // Почтовый индекс склада

    /** @var string|null  */
    private $city = null; // Наименование города

    /** @var string|null  */
    private $street = null; // Наименование улицы

    /** @var string|null  */
    private $house = null; // Номер дома

    /** @var string|null  */
    private $latitude = null; // Широта

    /** @var string|null  */
    private $longitude = null; // Долгота

    /** @var string|null  */
    private $phone = null; // Контактный телефон в формате +7**********

    /** @var string|null  */
    private $time_zone = null; // Часовой пояс

    /** @var array  */
    private $working_days = []; // Рабочие дни

    /**
     * Формирует массив параметров для запроса к API
     * @return array
     */
    public function asArr()
    {
        $params = [];
        $params['name'] = $this->name;
        $params['countryId'] = $this->country_id;
        $params['regionCode'] = $this->region_code;
        $params['federalDistrict'] = $this->federal_district;
        $params['region'] = $this->region;
        $params['index'] = $this->zip_code;
        $params['city'] = $this->city;
        $params['street'] = $this->street;
        $params['houseNumber'] = $this->house;
        $params['coordinates'] = $this->latitude.', '.$this->longitude;
        $params['contactPhoneNumber'] = $this->phone;
        $params['timeZone'] = $this->time_zone;

        $params['partnerLocationId'] = $this->id;

        /** @var WorkingDay $item */
        foreach ($this->working_days as $key => $working_day) {
            $params['workingTime'][] = $working_day->asArr();
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
     * @return string|null
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @param string|null $country_id
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * @return int
     */
    public function getRegionCode()
    {
        return $this->region_code;
    }

    /**
     * @param int $region_code
     */
    public function setRegionCode($region_code)
    {
        $this->region_code = $region_code;
    }

    /**
     * @return string|null
     */
    public function getFederalDistrict()
    {
        return $this->federal_district;
    }

    /**
     * @param string|null $federal_district
     */
    public function setFederalDistrict($federal_district)
    {
        $this->federal_district = $federal_district;
    }

    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getZipCode()
    {
        return $this->zip_code;
    }

    /**
     * @param string|null $zip_code
     */
    public function setZipCode($zip_code)
    {
        $this->zip_code = $zip_code;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * @param string|null $house
     */
    public function setHouse($house)
    {
        $this->house = $house;
    }

    /**
     * @return string|null
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string|null $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string|null
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string|null $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
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
    public function getTimeZone()
    {
        return $this->time_zone;
    }

    /**
     * @param string|null $time_zone
     */
    public function setTimeZone($time_zone)
    {
        $this->time_zone = $time_zone;
    }

    /**
     * @return array
     */
    public function getWorkingDays()
    {
        return $this->working_days;
    }

    /**
     * @param WorkingDay $workingDay
     */
    public function setWorkingDay($workingDay)
    {
        $this->working_days[] = $workingDay;
    }
}