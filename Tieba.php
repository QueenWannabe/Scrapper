<?php
/*
 * This file are used for scrapping specific website content from www.tieba.baidu.com with given key words.
 * author: lena li
 */
include("snoopy.php");

//@TODO: $KEY_WORD into array of key words.
$KEY_WORD = "百度手机输入法";
//max number of page to crawl from.
$MAX_PAGE = 2;
//content file name to be saved to
$FILE_NAME = "baidu_data/tieba.txt";

$word = urlencode(iconv('UTF-8', 'GB2312', $KEY_WORD));
$snoopy = new Snoopy;

for ($page = 0 ; $page < $MAX_PAGE; $page++) {

    $url = "http://tieba.baidu.com/f?kw=".$KEY_WORK."&ie=utf-8&pn=".($page*50);

    $snoopy->fetch($url);
    $file = $snoopy->results;

    preg_match_all("'<span class=\"tb_icon_author \"[\/\!]*?[^<>]*?>'si", $file, $authors);
    preg_match_all("'<a[\/\!]*?[^<>]* class=\"j_th_tit\"?>'si", $file, $links);
    preg_match_all("'<span class=\"threadlist_reply_date j_reply_data\"[\/\!]*?[^<>]*?>.*?</span>'si", $file, $dates);

    $author = $authors[0];
    $link = $links[0];
    $date = $dates[0];
    if (count($date)< count($author)) {
        $less = count($author) - count($date);
        for ($i=0; $i < $less; $i++) { 
            array_unshift($date, "");
        }
    }

    $href = array();
    for ($i = 0; $i < count($author); $i++) {
        
        $titlePos = strpos($author[$i], 'title="');
        $dataPos = strpos($author[$i], 'data-field="');

        $title = substr($author[$i], $titlePos+strlen('title="'), $dataPos-$titlePos-4);
        $titleIndex = strpos($title, ':');

        $title = substr($title, $titleIndex+1);

        $href[$i]['author'] = $title;

        $hrefPos = strpos($link[$i], 'href="');
        $titlePos = strpos($link[$i],'title="');
        $targetPos = strpos($link[$i], 'target="');

        $hrefLink = substr($link[$i], $hrefPos+strlen('href="'), $titlePos-$hrefPos-strlen('title="'));
        $title = substr($link[$i], $titlePos+strlen('title="'), $targetPos-$titlePos-strlen('target="'));

        $href[$i]['link'] = str_replace("\"", "", $hrefLink);
        $href[$i]['title'] = str_replace("\"", "", $title);

        $update = "";
        if (!empty($date[$i])) {
            $datePos = strpos($date[$i], "\">");
            $update = substr($date[$i], $datePos+2);
            $update = str_replace('</span>', '', $update);
        }
        $href[$i]['date'] = trim($update);
    }
    file_put_contents($FILE_NAME, json_encode($href), FILE_APPEND);
}
?>
