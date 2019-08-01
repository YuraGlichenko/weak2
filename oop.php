<?php
trait Driver
{

}

trait Gps
{

}

interface iTariff
{
    public function getPrice($km, $minutes, $age);
}

abstract class Tariff implements iTariff
{
    private $coefficient;
    private $pricePerKm;
    private $pricePerMinute;
    public $price;
    private $age;

    protected function setCoefficient($age)
    {
        if ($age >= 18 && $age <= 21) {
            $this->coefficient =  1.1;
        } elseif ($age < 18 || $age > 65) {
            $this->coefficient = 0;
        }else {
            $this->coefficient = 1;
        }
        $this->age = $age;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function getPrice($km, $minutes, $age)
    {
        $this->setCoefficient($age);

        if (empty($this->coefficient)) {
            $this->price = "Не выдаем <br />";
            echo $this->price;
        } else {
            $this->price = ($km * $this->pricePerKm + $this->pricePerMinute * $minutes) * $this->coefficient;
            print("с вас {$this->price} рублей <br /> ");
        }
    }

    protected function __construct($costPerKm, $costPerMinutes)
    {
        $this->pricePerMinute = $costPerMinutes;
        $this->pricePerKm = $costPerKm;
    }
}


class TariffBase extends Tariff
{
    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

}


class TariffPerHour extends Tariff
{
    
    public $costHour;

    public function getPricePerMinutes($hoursInMinutes)
    {
        $this->costHour = ceil($hoursInMinutes / 60);
    }

    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

    public function getPrice($km, $minutes, $age)
    {
        $this->getPricePerMinutes($minutes);
        $minutes = $this->costHour;
        parent::getPrice($km, $minutes, $age);
    }
}

class TariffStudents extends Tariff
{
    final function setCoefficient($age)
    {
        if ($age  <= 25) {
            parent::setCoefficient($age);
        } else {
            echo 'Вы не студент <br />';
        }
    }

    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

}

class Tariff24 extends Tariff
{
    public $hours;
    public $minutes;
    public $days;

    public function __construct($costPerKm, $costPer24, $hour, $minutes, $km, $age)
    {
        $this->minutes = $minutes;
        $this->hours = $hour;
        $this->days = floor($this->hours / 24);
        if ($minutes >= 30) {
            $this->days += 1;
        }
        parent::__construct($costPerKm, $costPer24);
        parent::getPrice($km, $this->days, $age);
    }

    /*    public function getPrice($km, $days, $age)
        {*/




}

$base = new TariffBase(10, 3);
$base->getPrice(10, 157, 20);

$baseHours = new TariffPerHour(0,200);
$baseHours->getPrice(10,267,18);

$baseStudent = new TariffStudents(4,1);
$baseStudent->getPrice(10,267,26);

$base24 = new Tariff24(1,1000, 24, 30, 18, 18);
