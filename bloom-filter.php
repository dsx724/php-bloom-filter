<?php
/*
https://github.com/Vaizard/MurmurHash3PHP
SHA512

m bits
k hashes
n values

probability of false positive
(1-e^(kn/m))^k


//(1-(1-1/x)^(x*ln(2)))^(x*ln(2)/n)=p
*/
class BloomFilter {
	const OPTIMIZE_FOR_MEMORY = 1; // smallest m
	const OPTIMIZE_FOR_HASHES = 2; // smallest k
	public static function createFromFPR($n, $fpr, $method = 0){
		if ($fpr <= 0 || $fpr >= 1) throw new Exception('Invalid false positive rate requested.');
		if ($method){
			
		} else {
		//	$m = ;
		//	$k = ;
		}
		//$m = ;
		//$k = ;
		return new BloomFilter($m,$k);
	}
	public static function union($bf1,$bf2){
		
	}
	public static function intersect($bf1,$bf2){
		
	}
	
	private $n; // # of entries
	private $m; // # of bits in array
	private $k; // # of hash functions
	private $bit_array;
	public function __construct($bit_array_length,$hash_count){
		if ($bit_array_length < 8) throw new Exception('For practical applications, we restrict the bit array length to at least 8 bits.');
		if ($bit_array_length & ($bit_array_length - 1) == 0) throw new Exception('The bit array must be power of 2.');
		
		$this->m = $bit_array_length; //number of bits
		$this->k = $hash_count;
	}
	public function getFalsePositiveRate($n = 0){
		// return pow(1-exp($this->k*$this->n/$this->m),$this->k); //approximate estimator
		return pow(1-pow(1-1/$this->m,$this->k*($n ?: $this->n)),$this->k); //accurate estimator
	}
	public function getBitArrayLength(){
		return $this->m;
	}
	public function add($key){
		
	}
}
$bf1 = new BloomFilter(256,2);
echo $bf1->getFalsePositiveRate().PHP_EOL;
echo $bf1->getFalsePositiveRate(128).PHP_EOL;
?>