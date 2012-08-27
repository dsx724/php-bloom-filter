<form action="" method="POST">
<input type="submit" name="bench" value="2"/>
<input type="submit" name="bench" value="10"/>
<input type="submit" name="bench" value="U"/>
<input type="submit" name="bench" value="I"/>
<input type="submit" name="bench" value=""/>
</form>
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

if (isset($_POST['bench'])){
	switch ($_POST['bench']){
		case '2':
			for ($i = 3; $i < 14; $i++){
				for ($j = 0.1; $j > 0.000001; $j /= 10){
					$n = (int)pow(2,$i);
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
		break;
		case '10':
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
		break;
		case 'U':
			$max = 1000000;
			$sample = 10000;
			$bf1 = BloomFilter::createFromProbability($max, 0.01);
			for ($i = 0; $i < $sample; $i++) $bf1->add('K'.$i);
			$bf2 = BloomFilter::createFromProbability($max, 0.01);
			for ($i = 0; $i < $sample; $i++) $bf1->add('K'.($i*2));
			$bfx = BloomFilter::getUnion($bf1,$bf2);
			echo $bfx->check(0);
			$bf3 = BloomFilter::createFromProbability($max, 0.1);
			for ($i = 0; $i < $sample; $i++) $bf1->add($i);
		break;
		case 'I':
			
		break;
		default:
			
		break;
	}
}
?>
</pre>