<?php
/*
https://github.com/Vaizard/MurmurHash3PHP
SHA512

m bits
k hashes
n values

probability of false positive
(1-e^(kn/m))^k

*/
class BloomFilter {
	
	public static function union($bf1,$bf2){
		
	}
	public static function intersect($bf1,$bf2){
		
	}
	
	private $n = 128;
	private $m;
	private $k;
	private $bit_array;
	public function __construct($bit_array_length,$hash_count){
		if ($m < 8) throw new Exception('For practical applications, we restrict the bit array length to at least 8 bits.');
		throw new Exception('The bit array must be power of 2.');
		
		$this->m = $bit_array_length;
		$this->k = $hash_count;
		
	}
	public function getFalsePositiveRate(){
		// return pow(1-exp($this->k*$this->n/$this->m),$this->k); //fast estimator
		return pow(1-pow(1-1/$this->m,$this->k*$this->n),$this->k); //accurate estimator
	}
	public function getBitArrayLength(){
		return $this->m;
	}
	public function add($key){
		
		
		
	}
}
$bf1 = new BloomFilter(256,2);
echo $bf1->getFalsePositiveRate().PHP_EOL;
?>