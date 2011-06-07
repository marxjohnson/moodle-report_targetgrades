<?php 
$dir = scandir('../');
sort($dir);
foreach ($dir as $d) {
    $fileinfo = pathinfo($d);
    if (is_file('../'.$d)) {
        if ($d != 'index.html' && $fileinfo['extension'] == 'html') {
            $contents = file_get_contents('../'.$d);
            preg_match('/<h2>(.+?)<\/h2>/', $contents, $matches);            
            echo '<li><a href="'.$fileinfo['basename'].'">'.$matches[1].'</a></li>'.PHP_EOL;
        }
    }
}
?>