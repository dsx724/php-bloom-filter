<form action="" method="POST">
<input type="submit" name="bench" value=""/>
<input type="submit" name="bench" value="2"/>
<input type="submit" name="bench" value="10"/>
<input type="submit" name="bench" value="U"/>
<input type="submit" name="bench" value="I"/>
</form>
<pre>
<?php
ini_set('display_errors','on');
ini_set('memory_limit','2G');
ini_set('max_execution_time',600);
error_reporting(E_ALL);
ini_set('display_errors',1);
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
	die($errno.': '.$errstr.' in '.$errfile.' on '.$errline);
});

require_once 'bloomfilter.php';

if (isset($_POST['bench'])){
	switch ($_POST['bench']){
		case '2':
			for ($i = 4; $i < 14; $i++){
				for ($j = 0.1; $j > 0.000001; $j /= 10){
					$n = (int)pow(2,$i);
					$bf = BloomFilter::createFromProbability($n, $j);
					echo 'N: '.$n."\tP: ".$j;
					echo $bf->calculateCapacity($j) < $n ? PHP_EOL.$bf->getInfo($j) : ' OK ';
					for ($k = 0; $k < $n; $k++) $bf->add('T'.$k*3);
					$false_neg = 0;
					$false_pos = 0;
					for ($k = 0; $k < $n*3; $k++) {
						if ($k % 3 == 0) $false_neg += !$bf->contains('T'.$k);
						else $false_pos += $bf->contains('T'.$k);
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
						if ($k % 3 == 0) $false_neg += !$bf->contains('T'.$k);
						else $false_pos += $bf->contains('T'.$k);
					}
					echo ' '.$false_neg.' '.$false_pos.' '.number_format($false_pos / $n / 3,2).' '.($false_pos / $n / 3 < $j && $false_neg == 0) ? 'PASS' : 'FAIL';
					echo PHP_EOL;
				}
			}
		break;
		case 'U':
			$capacity = 10000;
			$sample = 10000;
			$probability = 0.01;
			$bf1 = BloomFilter::createFromProbability($capacity, $probability);
			for ($i = 0; $i < $sample; $i++) $bf1->add('K'.$i);
			$bf2 = BloomFilter::createFromProbability($capacity, $probability);
			for ($i = 10000; $i < $sample * 2; $i+=2) $bf2->add('K'.$i);
			$bfx = BloomFilter::getUnion($bf1,$bf2);
			$false_neg = 0;
			$false_pos = 0;
			for ($i = 0; $i < $sample * 2 + 1; $i++){
				if ($i < $sample || ($i < $sample * 2 && $i % 2 == 0)) $false_neg += !$bfx->contains('K'.$i);
				else $false_pos += $bfx->contains('K'.$i);
			}
			echo $bfx->getInfo(0.01);
			echo 'False Negatives '.$false_neg.PHP_EOL;
			echo 'False Positives '.$false_pos.' ('.($false_pos / ($sample * 2 + 1)).','.$probability.')'.PHP_EOL;
			echo ($false_neg > 0 || $false_pos / ($sample * 2 + 1) > $probability) ? 'FAIL' : 'PASS';
			echo PHP_EOL;	
			
			try {
				echo 'Testing Merge ';
				$bf3 = BloomFilter::createFromProbability($capacity, 0.1);
				for ($i = 0; $i < $sample; $i++) $bf3->add($i);
				$bfx = BloomFilter::getUnion($bf1,$bf3);
				echo 'FAIL'.PHP_EOL;
			} catch (Exception $e){
				echo 'PASS'.PHP_EOL;
			}
			
			$bf1->union($bf2);
			$false_neg = 0;
			$false_pos = 0;
			for ($i = 0; $i < $sample * 2 + 1; $i++){
				if ($i < $sample || ($i < $sample * 2 && $i % 2 == 0)) $false_neg += !$bf1->contains('K'.$i);
				else $false_pos += $bf1->contains('K'.$i);
			}
			echo $bf1->getInfo(0.01);
			echo 'False Negatives '.$false_neg.PHP_EOL;
			echo 'False Positives '.$false_pos.' ('.($false_pos / ($sample * 2 + 1)).','.$probability.')'.PHP_EOL;
			echo ($false_neg > 0 || $false_pos / ($sample * 2 + 1) > $probability) ? 'FAIL' : 'PASS';
			echo PHP_EOL;
				
		break;
		case 'I':
			$capacity = 10000;
			$sample = 10000;
			$probability = 0.01;
			$bf1 = BloomFilter::createFromProbability($capacity, $probability);
			for ($i = 0; $i < $sample; $i++) $bf1->add('K'.$i);
			$bf2 = BloomFilter::createFromProbability($capacity, $probability);
			for ($i = 9000; $i < $sample * 2; $i+=2) $bf2->add('K'.$i);
			$bfx = BloomFilter::getIntersection($bf1,$bf2);
			$false_neg = 0;
			$false_pos = 0;
			for ($i = 0; $i < $sample * 2; $i++){
				if ($i >= 9000 && $i < 10000 && $i % 2 == 0) $false_neg += !$bfx->contains('K'.$i);
				else $false_pos += $bfx->contains('K'.$i);
			}
			echo $bfx->getInfo(0.01);
			echo 'False Negatives '.$false_neg.PHP_EOL;
			echo 'False Positives '.$false_pos.' ('.($false_pos / ($sample * 2 + 1)).','.$probability.')'.PHP_EOL;
			echo ($false_neg > 0 || $false_pos / ($sample * 2 + 1) > $probability) ? 'FAIL' : 'PASS';
			echo PHP_EOL;
				
			try {
				echo 'Testing Merge ';
				$bf3 = BloomFilter::createFromProbability($capacity, 0.1);
				for ($i = 0; $i < $sample; $i++) $bf3->add($i);
				$bfx = BloomFilter::getIntersection($bf1,$bf3);
				echo 'FAIL'.PHP_EOL;
			} catch (Exception $e){
				echo 'PASS'.PHP_EOL;
			}
				
			$bf1->intersect($bf2);
			$false_neg = 0;
			$false_pos = 0;
			for ($i = 0; $i < $sample * 2; $i++){
				if ($i >= 9000 && $i < 10000 && $i % 2 == 0) $false_neg += !$bf1->contains('K'.$i);
				else $false_pos += $bf1->contains('K'.$i);
			}
			echo $bf1->getInfo(0.01);
			echo 'False Negatives '.$false_neg.PHP_EOL;
			echo 'False Positives '.$false_pos.' ('.($false_pos / ($sample * 2 + 1)).','.$probability.')'.PHP_EOL;
			echo ($false_neg > 0 || $false_pos / ($sample * 2 + 1) > $probability) ? 'FAIL' : 'PASS';
			echo PHP_EOL;
		break;
		default:
			
		break;
	}
}
?>
</pre>