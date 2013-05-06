<?php
error_reporting(E_ALL);
set_error_handler(function ($errno, $errstr, $errfile, $errline) { die($errno.': '.$errstr.' in '.$errfile.' on '.$errline); });

$config = parse_ini_file('test.ini',true);

foreach ($config['php'] as $key => $value) ini_set($key,$value);
foreach ($config['test']['include'] as $include) require_once $include;

$_POST['bench'] = '10';

if (isset($_POST['bench'])){
    $results = [];
    switch ($_POST['bench']){
        case '2':
            $results[] = ['N Elements','EProb','M Bytes','FNeg','FPos','AProb'];
            for ($i = 4; $i < 16; $i++){
                $n = (int)pow(2,$i);
                for ($p = 0.1; $p > 0.000001; $p /= 10){
                    $result = [];
                    $result[] = $n;
                    $result[] = $p;

                    $filter = new $config['test']['class']($n, $p);
                    $result[] = $filter->getFilterSizeInBytes();

                    $false_neg = 0;
                    $false_pos = 0;

                    $range = $n * 3;
                    for ($k = 0; $k < $range; $k+= 3) $filter->add('T'.$k);
                    $samples = $n * 9;
                    for ($k = 0; $k < $samples; $k++) {
                        if ($k % 3 == 0 && $k < $range) $false_neg += !$filter->contains('T'.$k);
                        else $false_pos += $filter->contains('T'.$k);
                    }

                    $result[] = $false_neg;
                    $result[] = $false_pos;
                    $result[] = $false_pos / $samples;
                    $result[] = ($false_pos / $samples < $p && $false_neg == 0) ? '<i>PASS</i>' : '<b>FAIL</b>';
                    $results[] = $result;
                }
            }

        break;

        case '10':
            $results[] = ['N Elements','EProb','M Bytes','FNeg','FPos','AProb'];
            for ($i = 1; $i < 6; $i++){
                $n = (int)pow(10,$i);
                for ($p = 0.1; $p > 0.000001; $p /= 10){
                    $result = [];
                    $result[] = $n;
                    $result[] = $p;

                    $filter = new $config['test']['class']($n, $p);

                    $result[] = $filter->getFilterSizeInBytes();

                    $false_neg = 0;
                    $false_pos = 0;

                    $range = $n * 3;
                    for ($k = 0; $k < $range; $k+= 3) $filter->add('T'.$k);
                    $samples = $n * 9;
                    for ($k = 0; $k < $samples; $k++) {
                        if ($k % 3 == 0 && $k < $range) $false_neg += !$filter->contains('T'.$k);
                        else $false_pos += $filter->contains('T'.$k);
                    }

                    $result[] = $false_neg;
                    $result[] = $false_pos;
                    $result[] = $false_pos / $samples;
                    $result[] = ($false_pos / $samples < $p && $false_neg == 0) ? '<i>PASS</i>' : '<b>FAIL</b>';
                    $results[] = $result;
                }
            }
        break;

        case 'TEST':
            $capacity = 1000000;
            $probability = 0.01;
            $s1 = microtime(true);
            $filter = new $config['test']['class']($capacity, $probability);
            $e1 = microtime(true);

            $sample = 1000000;
            $offset = 500000;

            $s2 = microtime(true);
            for ($i = 0; $i < $sample; $i++) $filter->add($i);
            $e2 = microtime(true);
            $t = 0;
            $s3 = microtime(true);
            for ($i = $offset; $i < $sample + $offset; $i++) $t += $filter->contains($i);
            $e3 = microtime(true);

            echo '<pre>';
            echo 'Create Time: '.($e1 - $s1).PHP_EOL;
            echo 'Add Time: '.($e2 - $s2).' ('.floor($sample/($e2-$s2)).' i/s)'.PHP_EOL;
            echo 'Check Time: '.($e3 - $s3).' ('.floor($sample/($e3-$s3)).' i/s)'.PHP_EOL;
            echo $t;
            echo '</pre>';
        break;
    }
    echo '<table>';
    array_walk($results,function($row){ echo '<tr><td>'.implode('</td><td>',$row).'</td></tr>'; });
    echo '</table>';
}
