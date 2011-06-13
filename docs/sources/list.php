<?php 
$dir = scandir('../');
sort($dir);
$fileinfo = array();
$index_links = array();
$anchor_links = array();
foreach ($dir as $d) {
    $fileinfo[$d] = pathinfo($d);
    if (is_file('../'.$d)) {
        if ($d != 'index.html' && $fileinfo[$d]['extension'] == 'html') {
            $filename = $fileinfo[$d]['basename'];
            $contents = file_get_contents('../'.$d);
            preg_match('/<h2>(.+?)<\/h2>/', $contents, $matches);
            if (!empty($matches)) {
                $index_links[$filename] = $matches[1];
                $anchor_links[$filename] = array();
                $anchor_links[$filename]['top'] = array();
                preg_match_all('/<h3><a name="(.+?)">(.+?)<\/a><\/h3>/', $contents, $matches,PREG_SET_ORDER);
                if (!empty($matches[0])) {
                    foreach ($matches as $key => $match) {
                        $anchor_links[$filename]['top'][$match[1]] = $match[2];
                        $heading = strstr($contents, $match[0]);
                        if (isset($matches[$key+1])) {
                            $heading = strstr($heading, $matches[$key+1][0], true);
                        }
                        preg_match_all('/<h4><a name="(.+?)">(.+?)<\/a><\/h4>/', $heading, $subs, PREG_SET_ORDER);
                        foreach ($subs as $sub) {
                            $anchor_links[$filename][$match[1]][$sub[1]] = $sub[2];
                        }
                    }
                }
            }
        }
    }
}

echo '### @export "index"'.PHP_EOL;
foreach ($index_links as $filename => $text) {
    echo '<li><a href="'.$filename.'">'.$text.'</a></li>'.PHP_EOL;
}

foreach($anchor_links as $filename => $links) {
    echo '### @export "'.$filename.'"'.PHP_EOL;
    $toplinks = array_shift($links);
    foreach ($toplinks as $anchor => $text) {
        echo '<li><a href="#'.$anchor.'">'.$text.'</a>'.PHP_EOL;
    }

    foreach ($links as $header => $ls) {
        echo '### @export "'.$filename.'_'.$header.'"'.PHP_EOL;
        foreach($ls as $anchor => $text) {
            echo '<li><a href="#'.$anchor.'">'.$text.'</a>'.PHP_EOL;
        }
    }
}




?>