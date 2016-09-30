<?php

class FootballRadar
{

    private $baseUrl = 'https://www.footballradar.com/';
    private $quizUrl;
    private $answerUrl;
    private $node;

    public function __construct($node = 'h3')
    {
        $this->init();
        $this->node = $node;
    }

    private function init()
    {
        $this->quizUrl   = $this->baseUrl . 'quiz/';
        $this->answerUrl = $this->quizUrl . 'answer/';
    }

    private function calculate()
    {
        $content = $this->fetch($this->quizUrl, array('cookiefile' => '/tmp/cookie.txt', 'timeout' => 60));
        preg_match('#\{(.*?)\}#', $content, $match);

        return $calculation = eval("return ($match[1]);");
    }

    public function getAnswer()
    {
        $answerUrl = $this->answerUrl . $this->calculate();
        echo $this->fetch($answerUrl, array('cookiefile' => '/tmp/cookie.txt', 'timeout' => 60));
    }

    private function fetch($url, $z = null)
    {
        $ch = curl_init();

        $useragent = isset($z[ 'useragent' ]) ? $z[ 'useragent' ] : 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:10.0.2) Gecko/20100101 Firefox/10.0.2';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, isset($z[ 'post' ]));

        if (isset($z[ 'post' ])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $z[ 'post' ]);
        }
        if (isset($z[ 'refer' ])) {
            curl_setopt($ch, CURLOPT_REFERER, $z[ 'refer' ]);
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (isset($z[ 'timeout' ]) ? $z[ 'timeout' ] : 5));
        curl_setopt($ch, CURLOPT_COOKIEJAR, $z[ 'cookiefile' ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $z[ 'cookiefile' ]);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

$then = microtime(true);

$fr = new FootballRadar();
$fr->getAnswer();
$now = microtime(true);

echo sprintf("\n Elapsed:  %f", $now - $then);

?>
