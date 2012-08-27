<pre>
<?php
ini_set('display_errors','on');
require_once 'bloom-filter.php';

for ($i = 1; $i < 8; $i++){
	for ($j = 0.5; $j > 0.000001; $j /= 2){
		$n = (int)pow(10,$i);
		$bf = BloomFilter::createFromProbability($n, $j);
		echo 'N: '.$n."\tP: ".$j;
		echo $bf->calculateCapacity($j) < $n ? PHP_EOL.$bf->getInfo($j) : ' OK';
		echo PHP_EOL;
	}
}
for ($i = 3; $i < 24; $i++){
	for ($j = 0.5; $j > 0.000001; $j /= 2){
		$n = (int)pow(2,$i);
		$bf = BloomFilter::createFromProbability($n, $j);
		echo 'N: '.$n."\tP: ".$j;
		echo $bf->calculateCapacity($j) < $n ? PHP_EOL.$bf->getInfo($j) : ' OK';
		echo PHP_EOL;
	}
}
die();

//$bf1 = BloomFilter::createFromProbability(100000000, 0.01);
$n = 10000000;
$p = 0.01;
echo 'Asking for '.number_format($n).' elements with a probability of '.$p.PHP_EOL;
$bf1 = BloomFilter::createFromProbability($n, $p);
echo $bf1->getInfo(0.01);
//echo $bf1->calculateProbability(26).PHP_EOL;
$max = 100000;
for ($i = 0; $i < $max; $i+=2) $bf1->add('Test'.$i);

$start1 = microtime(true);

for ($i = $max; $i > 0; $i--) $bf1->check('Test'.$i);

$end1 = microtime(true);
$elapsed1 = $end1 - $start1;
printf('%10.10f',$elapsed1);


?>
</pre>