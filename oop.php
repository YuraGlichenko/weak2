<?php

trait Driver
{
    public function orderDriver()
    {
        $this->price += 100;
    }
}

trait Gps
{
    public $gps = 15;
    public $hour = 0;

    public function orderGps($minutes)
    {
        if ($minutes) {
            $this->hour = ceil($minutes / 60);
        }
        $this->price += $this->hour * $this->gps;
    }
}

interface iTariff
{
    public function getPrice($km, $minutes, $age, $gps = false, $drive = false);
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
            $this->coefficient = 1.1;
        } elseif ($age < 18 || $age > 65) {
            $this->coefficient = 0;
        } else {
            $this->coefficient = 1;
        }
        $this->age = $age;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function getPrice($km, $minutes, $age, $gps = false, $drive = false)
    {
        $this->setCoefficient($age);

        if (empty($this->coefficient)) {
            $this->price = "Не выдаем <br />";
            echo $this->price;
        } else {
            $this->price = ($km * $this->pricePerKm + $this->pricePerMinute * $minutes) * $this->coefficient;
            //print("с вас {$this->price} рублей <br /> ");
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
    use Gps;

    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

    public function getPrice($km, $minutes, $age, $gps = false, $drive = false)
    {
        parent::getPrice($km, $minutes, $age, $gps, $drive);

        if (isset($gps)) {
            if (!empty($minutes)) {
                $this->orderGps($minutes);
            }
        }

        print("с вас {$this->price} рублей <br /> ");
    }

}


class TariffPerHour extends Tariff
{
    use Driver, Gps;

    public $costHour;

    public function getPricePerMinutes($hoursInMinutes)
    {
        $this->costHour = ceil($hoursInMinutes / 60);
    }

    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

    public function getPrice($km, $minutes, $age, $gps = false, $drive = false)
    {
        $minute = $minutes;
        $this->getPricePerMinutes($minutes);
        $minutes = $this->costHour;
        parent::getPrice($km, $minutes, $age, $gps, $drive);

        if (isset($drive)) {
            $this->orderDriver();
        }

        if (isset($gps)) {
            $this->orderGps($minute);
        }
        print("с вас {$this->price} рублей <br /> ");
    }
}

class TariffStudents extends Tariff
{
    use Gps;

    public function __construct($costPerKm, $costPerMinutes)
    {
        parent::__construct($costPerKm, $costPerMinutes);
    }

    public function getPrice($km, $minutes, $age, $gps = false, $drive = false)
    {
        if ($age <= 25) {

            parent::getPrice($km, $minutes, $age);

            if (!empty($gps)) {
                $this->orderGps($minutes);
            }
            print("с вас {$this->price} рублей <br /> ");
        } else {
            print('Вы не студент <br />');
        }
    }
}

class Tariff24 extends Tariff
{
    use Driver, Gps;

    public $hours;
    public $minutes;
    public $days;

    public function __construct($costPerKm, $costPer24, $hour, $minutes, $km, $age, $driver = false, $gps = false)
    {
        $this->minutes = $minutes;
        $this->hours = $hour;
        $this->days = floor($this->hours / 24);
        if ($minutes >= 30) {
            $this->days += 1;
        }
        parent::__construct($costPerKm, $costPer24);
        parent::getPrice($km, $this->days, $age);

        if (!empty($driver)) {
            $this->orderDriver();
        }

        if (!empty($gps)) {
            $this->hour = $this->hours * 60;
            $this->orderGps($this->hour);
        }

        print("с вас {$this->price} рублей <br /> ");
    }

    /*    public function getPrice($km, $days, $age)
        {*/


}

$base = new TariffBase(10, 3);
$base->getPrice(10, 157, 20, 1);

$baseHours = new TariffPerHour(0, 200);
$baseHours->getPrice(10, 267, 18, 1, 1);

$baseStudent = new TariffStudents(4, 1);
$baseStudent->getPrice(10, 267, 22, 1);

$base24 = new Tariff24(1, 1000, 24, 30, 18, 18, 0, 1);
