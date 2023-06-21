<?php

//Подключение файла конфигурации
require_once "config.php";

//Проверка блокировки файла функцией flock для исключения дублирования процесса работы скрипта
if (PR_FLOCK_USE) {
    $lock = fopen(__DIR__ . "/" . pathinfo(__FILE__, PATHINFO_FILENAME) . ".lock", "w");
    if (!($lock && flock($lock, LOCK_EX | LOCK_NB))) {
        exit("Скрипт уже запущен" . PHP_EOL);
    }
}

if ($_SERVER["argc"] > 1) {
    $task = $_SERVER["argv"][1]; //При передаче аргумента c сервера, ex. URL: https://domain.tld/app.php check_new_items or CRON with CLI: /www/php/7.4/bin/php -f /www/username/domain.tld/app.php check_new_items
} else {
    $task = "check_new_items"; //При локальной проверке
}

if ($task == "check_new_items") {
    (new HDRezka())->checkNewItems();
} else {
    die("Hacking attempt!");
}
