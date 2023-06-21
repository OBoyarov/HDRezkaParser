<?php
class HDRezka {

    protected string $host;
    protected object $db_obj;

    function __construct() {
        $this->host = PR_HOST;
        $this->db_obj = new MySQL();
    }

    function checkNewItems() {
        $rows = $this->db_obj->getData("SELECT url FROM hdrezka");
        $items_url = array();
        foreach ($rows as $row) {
            array_push($items_url, $row["url"]);
        }
        for ($i = PR_PAGES_COUNT; $i > 0; $i--) {
            $url = "https://$this->host/page/$i/?filter=last";
            $page = getResponseFromHDRezka($url, $this->host);
            if (strlen($page) > 0) {
                preg_match_all('/"b-content__inline_item"(?P<items>.*?)<\/div><\/div>/', $page, $matches_items);
                foreach (array_reverse($matches_items["items"]) as $matches_item) {
                    preg_match('/data-id="(?P<hdrezka_id>.*?)" data-url="(?P<url>.*?)"(.+?)img src="(?P<thumb>.*?)"(.+?)entity">(?P<type>.*?)</', $matches_item, $matches);
                    $item_url = $matches["url"];
                    $item_url = str_replace([$this->host, "https://", "http://"], "", $item_url);
                    if (!in_array($item_url, $items_url)) {
                        $item_id = $matches["hdrezka_id"];
                        $item_type = mb_strtolower($matches["type"]);
                        $item_thumb = $matches["thumb"];

                        $item_info = "";
                        preg_match('/info">(?P<info>.*?)</', $matches_item, $matches);
                        if (count($matches) > 0) {
                            $item_info = $matches["info"];
                        }

                        $item = getResponseFromHDRezka("https://$this->host" . $item_url, $this->host);
                        preg_match('/<h1 itemprop="name">(?P<title>.*?)<\/h1>/', $item, $matches);
                        $item_title = $matches["title"];

                        $item_alternative = "";
                        preg_match('/itemprop="alternativeHeadline">(?P<alternative_title>.*?)<\/div>/', $item, $matches);
                        if (count($matches) > 0) {
                            $item_alternative = $matches["alternative_title"];
                        }

                        preg_match('/b-sidecover(.+?)href="(?P<poster>.*?)"/', $item, $matches);
                        $item_poster = $matches["poster"];

                        $item_imdb_id = null;
                        preg_match('/b-post__info_rates imdb"><a href="\/help\/(?P<imdb_url>.*?)\/" target/', $item, $matches);
                        if (count($matches) > 0) {
                            $imdb_url = urldecode(base64_decode($matches["imdb_url"]));
                            preg_match('/imdb.com\/title\/(?P<imdb_id>.*?)\//', $imdb_url, $matches);
                            $item_imdb_id = $matches["imdb_id"];
                        }

                        $item_kp_id = null;
                        preg_match('/b-post__info_rates kp"><a href="\/help\/(?P<kp_url>.*?)\/" target/', $item, $matches);
                        if (count($matches) > 0) {
                            $kp_url = urldecode(base64_decode($matches["kp_url"]));
                            preg_match('/kinopoisk.ru\/film\/(?P<kp_id>.*?)\//', $kp_url, $matches);
                            $item_kp_id = $matches["kp_id"];
                        }

                        (new MySQL())->changeData(
                            "INSERT INTO hdrezka (hdrezka_id, url, title, alternative, type, imdb_id, kp_id, thumbnail, poster, info) 
                            VALUES 
                            (:hdrezka_id, :url, :title, :alternative, :type, :imdb_id, :kp_id, :thumbnail, :poster, :info) 
                            ON DUPLICATE KEY UPDATE url = :url, imdb_id = :imdb_id, kp_id = :kp_id",
                            [
                                "hdrezka_id" =>  $item_id,
                                "url" =>         $item_url,
                                "title" =>       $item_title,
                                "alternative" => $item_alternative,
                                "type" =>        $item_type,
                                "imdb_id" =>     $item_imdb_id,
                                "kp_id" =>       $item_kp_id,
                                "thumbnail" =>   $item_thumb,
                                "poster" =>      $item_poster,
                                "info" =>        $item_info
                            ]
                        );

                        (new Telegram())->sendMessage("<b>" . PR_NAME . ": Добавлен новый $item_type!</b>" . PHP_EOL . "https://$this->host" . $item_url);
                        echo PR_NAME . ": Добавлен новый $item_type: https://$this->host" . $item_url . PHP_EOL;
                        sleep(PR_PAUSE_SEC);
                    } else {
                        echo "Пропуск: $item_url" . PHP_EOL;
                    }
                }
            } else {
                die("Not result!");
            }
            sleep(PR_PAUSE_SEC * 5);
        }
    }

}