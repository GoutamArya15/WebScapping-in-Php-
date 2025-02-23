<?php
//
error_reporting(E_ALL);
include_once("/xampp/htdocs/simple-html-dom/simplehtmldom_1_9_1/simple_html_dom.php");
$scrap_url = "https://www.medschool.umaryland.edu/news/2025/";
$html = file_get_html($scrap_url);
$array = [];

$content = $html->find('div.content-wrapper div.inner-wrapper div.media');
foreach ($content as $item) {
    $date = '';
    $heading = '';
    $description = '';
    $sub_heading = '';
    $text = '';
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
    $description = $item->find("div.bd p", 1);
    $description = $description ? $description->innertext : '';
    $headingTag = $item->find("div.bd h3.h4 a", 0);
    $heading = $headingTag ? $headingTag->innertext : '';
    $href = $headingTag ? $headingTag->href : '';
    $full_url = strpos($href, 'http') === 0 ? $href : "https://www.medschool.umaryland.edu/" . $href;
    $html1 = file_get_html($full_url);

    if ($html1) {
        $img = $html1->find('div.alt-to-caption p');
        $images = [];
        $video = [];
        foreach ($img as $figure) {
            $content = $figure->outertext;
            $data =  preg_replace('/<img.*?>/i', '', $content);
            $text = $text . $data;
            $imgTag = $figure->find('img', 0);
            $iframe = $figure->find('div.video-responsive', 0);
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
            if (!empty($iframe_src) && !in_array(['video' => $iframe_src], $video) && count($video) > 0) {
                $video[] = [
                    'video' => $iframe_src,
                ];
            } else {
                $video = '';
            }
        }
        $array[] = [
            'date' => $date,
            'heading' => $heading,
            'description' => $description,
            'sub-heading' => '',
            'image' => $images,
            'content' => $text,
            'video' => $video,
        ];
    }
}

echo "<pre>";
print_r($array);
echo "</pre>";




