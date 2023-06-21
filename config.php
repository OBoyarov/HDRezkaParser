<?php
//ini_set('display_errors', 0);
ini_set("MAX_EXECUTION_TIME", 900);
ini_set("memory_limit", "1024M");

//INCLUDES
require_once "functions.php";
require_once "mysql.class.php";
require_once "telegram.class.php";
require_once "hdrezka.class.php";

//PROJECT
define("PR_NAME", "HDRezkaParser"); //Имя проекта
define("PR_HOST", "hdrezka.ag"); //Если сайт заблокирован, то использовать зеркало по запросу к mirror@hdrezka.org (отправив любое сообщение)
define("PR_FLOCK_USE", true); //Использование функции flock
define("PR_PAGES_COUNT", 3); //Кол-во первых страниц для парсинга
define("PR_PAUSE_SEC", 1); //Пауза в сек. перед следующим запросом

//DB
define("DB_HOST", ""); //Сервер базы данных
define("DB_NAME", ""); //Имя базы данных
define("DB_USER", ""); //Имя пользователя базы данных
define("DB_PASS", ""); //Пароль пользователя базы данных
checkTableExists(new MySQL()); //Создание таблицы в базе данных. При необходимости закомментировать или удалить

//TG
define("TG_USE", false); //Использование функций telegram бота
define("TG_TOKEN", ""); //Токен telegram бота
define("TG_ADMIN", ""); //Идентификатор админа telegram бота
checkUseTelegram(); // Проверка использования telegram бота. При необходимости закомментировать или удалить
