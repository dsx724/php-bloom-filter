<pre>
<?php
ini_set('display_errors','on');
ini_set('memory_limit','32G');
ini_set('max_execution_time',600);
error_reporting(E_ALL);
ini_set('display_errors',1);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	die($errno.': '.$errstr.' in '.$errfile.' on '.$errline);
});

require_once 'bloom-filter.php';

for ($i = 1; $i < 6; $i++){
	for ($j = 0.1; $j > 0.000001; $j /= 10){
		$n = (int)pow(10,$i);
		$bf = BloomFilter::createFromProbability($n, $j);
		echo 'N: '.$n."\tP: ".$j;
		echo $bf->calculateCapacity($j) < $n ? PHP_EOL.$bf->getInfo($j) : ' OK ';
		for ($k = 0; $k < $n; $k++) $bf->add('T'.$k*3);
		$false_neg = 0;
		$false_pos = 0;
		for ($k = 0; $k < $n*3; $k++) {
			if ($k % 3 == 0) $false_neg += !$bf->check('T'.$k);
			else $false_pos += $bf->check('T'.$k);
		}
		echo ' '.$false_neg.' '.$false_pos.' '.number_format($false_pos / $n / 3,2).' '.($false_pos / $n / 3 < $j && $false_neg == 0) ? 'PASS' : 'FAIL';
		echo PHP_EOL;
	}
}
for ($i = 3; $i < 14; $i++){
	for ($j = 0.1; $j > 0.000001; $j /= 10){
		$n = (int)pow(2,$i);
		$bf = BloomFilter::createFromProbability($n, $j);
		echo 'N: '.$n."\tP: ".$j;
		echo $bf->calculateCapacity($j) < $n ? PHP_EOL.$bf->getInfo($j) : ' OK';
		for ($k = 0; $k < $n; $k++) $bf->add('T'.$k*3);
		$false_neg = 0;
		$false_pos = 0;
		for ($k = 0; $k < $n*3; $k++) {
			if ($k % 3 == 0) $false_neg += !$bf->check('T'.$k);
			else $false_pos += $bf->check('T'.$k);
		}
		echo ' '.$false_neg.' '.$false_pos.' '.number_format($false_pos / $n / 3,2).' '.($false_pos / $n / 3 < $j && $false_neg == 0) ? 'PASS' : 'FAIL';
		echo PHP_EOL;
	}
}
?>
</pre>