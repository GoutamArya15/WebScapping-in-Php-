[6:41 PM, 2/22/2025] Himanshi Kurukshetra: <?php
error_reporting(E_ALL);
$root = dirname(_FILE_) . "/../../../../../";
$include_path = $root . "/lib/include/";
set_include_path($include_path);
include_once("htmlMimeMail.php");
include_once("DB.config.class.inc");
include("Query.class.inc");
include("misc.inc");
include_once("simple_html_dom.php");
$env = "test";
$inst_name = "Maryland School of Medicine";
$scrap_url = "https://www.medschool.umaryland.edu/news/2025/";
$html = file_get_html($scrap_url);
$arry = [];
$title1 = $subtitle1 = $content = $image1 = $video1 = $text1 = $contact1 = $tags1 = $img = $headline = '';
$content = $html->find('div.content-wrapper div.inner-wrapper div.media');
$cdate = date('Y-m-d', strtotime(' -15 day'));

foreach ($content as $item) {
    $date = '';
    $heading = '';
    $desc = '';
    $sub_heading = '';
    $text1 = '';
    $date1 = $item->find("div.bd p.h4", 0);
    $date1 = $date1 ? trim($date1->innertext) : '';
    if (strpos($date1, ',') !== false) {
        $arrdate = explode(',', $date1);
        $month_day = isset($arrdate[1]) ? trim($arrdate[1]) : '';
        $year = isset($arrdate[2]) ? trim($arrdate[2]) : '';
        $main_date = $month_day . ' ' . $year;
        $date = strtotime($main_date) ? date('Y-m-d', strtotime($main_date)) : '0000-00-00';
    } else {
        $date = '0000-00-00';
    }
	if ($date >= $cdate) {
    $desc1 = $item->find("div.bd p", 1);
    $desc = $desc1 ? $desc1->innertext : '';
    $headingTag = $item->find("div.bd h3.h4 a", 0);
    $heading = $headingTag ? $headingTag->innertext : '';
    $href = $headingTag ? $headingTag->href : '';
    $full_url = strpos($href, 'http') === 0 ? $href : "https://www.medschool.umaryland.edu/" . $href;
    $html1 = file_get_html($full_url);
    if ($html1) {
        $alt = $html1->find('section.silk-tabs__wrapper', 0);
        $sub =  $html1->find('div.alt-to-caption h3', 1);
        $sub_heading = $sub->plaintext;
        $main_paragraph = $html1->find('div.alt-to-caption p,h3');
        $alt_to_caption = $html1->find('div.alt-to-caption', 0);
        $images = [];
        $video = [];
        foreach ($alt_to_caption as $val) {
            $content = $val->outertext;
            $patterns = [
                '/<img\b[^>]*>/i',                                   // Remove <img> tags
                '/<h3\b[^>]>.?<\/h3>/is',
                '/<h2\b[^>]>.?<\/h2>/is',
                '/<section\b[^>]>.?<\/section>/is',
                '/<iframe\b[^>]>.?<\/iframe>/is',                         // Remove <h3> tags and their content
                '/<div[^>]\bclass=["\'][^"\']*silk-tabs[^"\']["\'][^>]>.?<\/div>/is', // Remove <div> elements with class "silk-tabs"
            ];
            $result = preg_replace($patterns, '', $content);
            $text1 .= $result;
        }


        foreach ($main_paragraph as $figure) {
            $imgTag = $figure->find('img', 0);
            $captionTag = $figure->find('img[alt]', 0);
            $imageSrc = $imgTag ? $imgTag->src : null;
            $captionText = $captionTag ? $captionTag->getAttribute('alt') : '';
            $iframe_src = $iframe ? $iframe->src : '';
            if (!empty($imageSrc) && !in_array(['img' => $imageSrc, 'caption' => $captionText], $images)) {
                $images[] = [
                    'img' => $imageSrc,
                    'caption' => $captionText,
                ];
            }
        }

        $arry[] = [
			'title' => $heading,
            'url' =>$full_url,
            'date' =>$date ,
            'description' => strip_tags($desc).'...',
            'content' =>$text1,
            'doiName' => "",
            'doiLink' => "",
            'pubWebsite' =>'',
            'gscholer' => "",
            'img'=>$images , 
            'citationLink' => '',
            'subhead'=>$sub_heading,   
            'caption'=>''
        ];
    }
}
}
	
echo "<pre>";
var_dump($arry);
die('sfdf');
