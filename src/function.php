<?php
function task1($xmlFile)
{
    function itemInfo($items)
    {
        foreach ($items as $item => $value) {
            echo "$item: $value <br />";
        }
    }

    if (!is_string($xmlFile)) die('строка не указана!');
    $fileData = file_get_contents($xmlFile);
    $xml = new SimpleXMLElement($fileData);
    $infoOrder = $xml->attributes();
    list($order, $orderDate) = $infoOrder;
    echo "<h2>Заказ: " . $order . " От: " . $orderDate, "</h2>";

    foreach ($xml->Address as $address) {
        if ($address->attributes()->Type == 'Shipping') {
            echo "<p><b>Отправитель: </b></p>" . PHP_EOL;
            itemInfo($address);
            continue;
        }
        echo "<p><b>Получатель: </b></p>" . PHP_EOL;
        itemInfo($address);
    }
    echo "<p><b>" . $xml->DeliveryNotes->getName() . ": </b>" . $xml->DeliveryNotes->__toString() . "</p>";

    foreach ($xml->Items->children() as $item) {
        echo "<p><b>" . $item->getName() . ": </b>", $item['PartNumber'] . "</b><br />";

        itemInfo($item);
    }
}

function task2()
{
    function randomArray() {
        $randomArray = [];

        for ($i = 0, $j = 0; $i < 10 && $j <= 1 ; $i++) {
            $randomArray[$j][] = random_int(1, 100);
            while ($i < 9) {
                continue 2;
            }
            $j++;
            $i = -1;
        }
        return $randomArray;
    }

    function saveJson($jsonData, $nameJsonFile)
    {
        file_put_contents($nameJsonFile,$jsonData);
    }

    function changeJson ($jsonFile)
    {
        $jsonFile = file_get_contents($jsonFile);
        $getArrayInJson = json_decode($jsonFile);
        if (rand(0,1)) {
            $countChangeItems = mt_rand(0, 2);
            for ($i = 0; $i < $countChangeItems; $i++) {
                $randomKey = mt_rand(0,1);
                $getArrayInJson[$randomKey][mt_rand(0, count($getArrayInJson[$randomKey]))] = random_int(0, 100) ;
            }
            saveJson(json_encode($getArrayInJson), 'output2.json');
        }

    }

    $arr = randomArray();
    $json = json_encode($arr);
    saveJson($json, 'output.json');
    changeJson('output.json');
    $json1_file = file_get_contents('output.json');
    $json1 = json_decode($json1_file, 1);
    $json2_file = file_get_contents('output2.json');
    $json2 = json_decode($json2_file, 1);


    echo "<pre>";
    echo var_dump (array_diff($json1[0], $json2[0]), 1);
}

function task3()
{
    function getRandomArray($iterable, &$array)
    {
        $numb = random_int(1, 100);
        $array[] = $numb;
        if ($iterable !== 0) {
                getRandomArray($iterable - 1, $array);
        }
    }
    function create_file_csv(string $nf, array $arr, string $delimiter):void
    {
        $fp = fopen($nf, 'wt');
        fputcsv($fp, $arr, $delimiter);
        fclose($fp);
    }
    function open_csv_file(string $file, string $delimeter, int $len):array
    {
        $fp = fopen($file, 'r');
        $file_csv = fgetcsv($fp, $len, $delimeter);
        return $file_csv;
    }
    $randomArray = [];
    getRandomArray(50, $randomArray);
    create_file_csv('test.csv', $randomArray, ';');
    $res = 0;
    foreach (open_csv_file('test.csv', ';', 300) as $value) {
        if ($value % 2 == 0) {
            $res += $value;
        }
    };
    echo $res;
}

function task4($file)
{

    $getFile = file_get_contents($file);
    $json = json_decode($getFile, 1);
    function search_key($searchKey, array $arr, array &$result)
    {
        if (isset($arr[$searchKey])) {
            $result[] = $arr[$searchKey];
        }
        foreach ($arr as $key => $param) {
            if (is_array($param)) {
                search_key($searchKey, $param, $result);
            }
        }
    }
    $res = [];
    search_key('title', $json, $res);
    foreach ($res as $k => $v) {
        echo $v.PHP_EOL;
    }
}