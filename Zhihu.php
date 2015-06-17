<?php
/*
 * This file are used for scrapping specific website content from www.zhihu.com with given key words.
 * author: lena li
 */
include("snoopy.php");

//@TODO: $KEY_WORD into array of key words.
$KEY_WORD = "百度手机输入法";
//max number of page to crawl from.
$MAX_PAGE = 2;
//content file name to be saved to
$FILE_NAME = "baidu_data/zhihu.txt";

$word = urlencode($KEY_WORD);
$snoopy = new Snoopy;

for ($page = 0 ; $page < $MAX_PAGE; $page++) {

    $url = "http://www.zhihu.com/r/search?q=".$word."&range=&type=question&offset=".$page*10;

    $snoopy->fetch($url);
    $file = $snoopy->results;

    $data = json_decode($file, true);
    
    $href = array();
    
    $hrefPattern = 'href="';
    $classPattern = 'class="question-link">';

    $hrefLen = strlen($hrefPattern);
    $classLen = strlen($classPattern);

    for ($i = 0; $i < count($data['htmls']); $i++) {
        $content = $data['htmls'][$i];

        preg_match_all("'<a[^>]*class=\"question-link\"*>.*?</a>'si", $content, $extract);
        $content = $extract[0][0];

        $linkPos = strpos($content, $hrefPattern);
        $classPos = strpos($content, $classPattern);

        $title = substr($content, $classPos+$classLen);
        $link = substr($content, $linkPos+$hrefLen, 18);
        
        $href[$i]['title'] = str_replace('</a>', '', $title);
        $href[$i]['link'] = $link;
    }

    file_put_contents($FILE_NAME, json_encode($href), FILE_APPEND);
}
?>
