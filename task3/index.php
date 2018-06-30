<?php
/**
 * Created by PhpStorm.
 * User: Igor
 * Date: 07.06.2018
 * Time: 20:34
 */
require_once('FileIterator.php');

$start = microtime(true);

try {	
    $dsn = new FileIterator('text.txt');
    echo $dsn->current(). "\n";
    /*Выбираем 10000 строк*/
	for($ap=0; $ap< 10000; $ap++){
        echo 'Pоsition:'.$ap.': ';
        $dsn->seek($ap);
        echo $dsn->current(). "\n";
    }
	
} catch (OutOfBoundsException $e) {
    echo $e->getMessage().PHP_EOL;
}

echo (microtime(true)-$start).' seconds';