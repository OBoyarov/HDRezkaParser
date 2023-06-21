<?php

function checkTableExists($db_obj) {
    $db_obj->changeData("
    CREATE TABLE IF NOT EXISTS hdrezka (
        hdrezka_id int(11) NOT NULL,
        url varchar(255) NOT NULL,
        title varchar(255) NOT NULL,
        alternative varchar(255) DEFAULT NULL,
        type varchar(50) DEFAULT NULL,
        imdb_id varchar(20) DEFAULT NULL,
        kp_id int(11) DEFAULT NULL,
        thumbnail varchar(255) DEFAULT NULL,
        poster varchar(255) DEFAULT NULL,
        info varchar(255) DEFAULT NULL,
        create_date datetime DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY url_UNIQUE (url)
    ) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4");
}

function checkUseTelegram() {
    if(!TG_USE) {
        echo "Отправка в telegram отключен, проверьте переменную TG_USE!" . PHP_EOL;
    }
}

function getResponseFromHDRezka($url, $host) {
    $rnd = substr(intval(microtime(true) * 1000),  -5);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $headers = array();
    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
    $headers[] = 'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7';
    $headers[] = 'Cache-Control: no-cache';
    $headers[] = 'Pragma: no-cache';
    $headers[] = "Host: $host";
    $headers[] = "Referer: https://$host/";
    $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.$rnd Safari/537.36";

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch) . PHP_EOL;
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode !== 200){
        (new Telegram())->sendMessage("HDRezka: Status code: $httpcode", "" , true);
        echo $result . PHP_EOL;
        die("Status code: $httpcode" . PHP_EOL);
    }
    return $result;

}
