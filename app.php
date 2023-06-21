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
    $task = $_SERVER["argv"][1]; //При передаче аргумента c сервера, ex. CRON with CLI: /opt/php/7.4/bin/php -f /var/www/username/data/www/domain.tld/app.php check_new_items
} else {
    $task = "check_new_items"; //При локальной проверке
}

if ($task == "check_new_items") {
    (new HDRezka())->checkNewItems();
} else {
    die("Hacking attempt!");
}
