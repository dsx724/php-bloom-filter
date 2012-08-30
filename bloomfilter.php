<?php
/* https://github.com/dsx724/php-bloom-filter */
require_once __DIR__.'/iamq.php';
class BloomFilter implements iAMQ {
	const OPTIMIZE_FOR_MEMORY = 1; // smallest m
	const OPTIMIZE_FOR_HASHES = 2; // smallest k
	public static function createFromProbability($n, $p, $method = 0){
		if ($p <= 0 || $p >= 1) throw new Exception('Invalid false positive rate requested.');
		if ($method){
			//TODO target optimizations
		} else {
			$k = floor(log(1/$p,2));
			$m = pow(2,ceil(log(-$n*log($p)/pow(log(2),2),2))); //approximate estimator method
		}
		return new BloomFilter($m,$k);
	}
	public static function getUnion($bf1,$bf2){
		if ($bf1->m != $bf2->m) throw new Exception('Unable to merge due to vector difference.');
		if ($bf1->k != $bf2->k) throw new Exception('Unable to merge due to hash count difference.');
		if ($bf1->hash != $bf2->hash) throw new Exception('Unable to merge due to hash difference.');
		$bf = new BloomFilter($bf1->m,$bf1->k,$bf1->hash);
		$bf->n = $bf1->n + $bf2->n;
		for ($i = 0; $i < strlen($bf->bit_array); $i++) $bf->bit_array[$i] = chr(ord($bf1->bit_array[$i]) | ord($bf2->bit_array[$i]));
		return $bf;
	}
	public static function getIntersection($bf1,$bf2){
		if ($bf1->m != $bf2->m) throw new Exception('Unable to merge due to vector difference.');
		if ($bf1->k != $bf2->k) throw new Exception('Unable to merge due to hash count difference.');
		if ($bf1->hash != $bf2->hash) throw new Exception('Unable to merge due to hash difference.');
		$bf = new BloomFilter($bf1->m,$bf1->k,$bf1->hash);
		$bf->n = $bf1->n + $bf2->n;
		for ($i = 0; $i < strlen($bf->bit_array); $i++) $bf->bit_array[$i] = chr(ord($bf1->bit_array[$i]) & ord($bf2->bit_array[$i]));
		return $bf;
	}
	private $n = 0; // # of entries
	private $m; // # of bits in array
	private $k; // # of hash functions
	private $hash;
	private $mask;
	private $m_chunk_size;
	private $bit_array;
	public function __construct($m, $k, $h='md5'){
		if ($m < 8) throw new Exception('For practical applications, we restrict the bit array length to at least 8 bits.');
		if ($m & ($m - 1) == 0) throw new Exception('The bit array must be power of 2.');
		if ($m > 17179869183) throw new Exception('The maximum filter size is 1GB');
		$this->m = $m; //number of bits
		$this->k = $k;
		$this->hash = $h;
		$address_bits = (int)log($m,2);
		$this->mask =(1 << $address_bits) - 1;
		$this->m_chunk_size = ceil($address_bits / 8);
		$this->bit_array = (binary)(str_repeat("\0",($this->m >> 3)));
	}
	public function calculateProbability($n = 0){
		return pow(1-pow(1-1/$this->m,$this->k*($n ?: $this->n)),$this->k);
		// return pow(1-exp($this->k*($n ?: $this->n)/$this->m),$this->k); //approximate estimator
	}
	public function calculateCapacity($p){
		return floor($this->m*log(2)/log($p,1-pow(1-1/$this->m,$this->m*log(2))));
	}
	public function getElementCount(){
		return $this->n;
	}
	public function getArraySize(){
		return $this->m;
	}
	public function getHashCount(){
		return $this->k;
	}
	public function getInfo($p = null){
		$units = array('','K','M','G','T','P','E','Z','Y');
		$M = $this->m >> 3;
		$magnitude = floor(log($M,1024));
		$unit = $units[$magnitude];
		$M /= pow(1024,$magnitude);
		return 'Allocated m: '.$this->m.' bits ('.$M.' '.$unit.'Bytes)'.PHP_EOL.
			'Allocated k: '.$this->k.PHP_EOL.
			'Load n: '.$this->n.PHP_EOL.
			(isset($p) ? 'Capacity ('.$p.'): '.number_format($this->calculateCapacity($p)).PHP_EOL : '');
	}
	public function add($key){
		$hash = hash($this->hash,$key,true);
		while ($this->m_chunk_size * $this->k > strlen($hash)) $hash .= hash($this->hash,$key,true);
		for ($index = 0; $index < $this->k; $index++){
			$hash_sub = hexdec(unpack('H*',substr($hash,$index*$this->m_chunk_size,$this->m_chunk_size))[1]) & $this->mask;
			$word = $hash_sub >> 3;
			$this->bit_array[$word] = chr(ord($this->bit_array[$word]) | 1 << ($hash_sub % 8));
		}
		$this->n++;
	}
	public function contains($key){
		$hash = hash($this->hash,$key,true);
		while ($this->m_chunk_size * $this->k > strlen($hash)) $hash .= hash($this->hash,$key,true);
		for ($index = 0; $index < $this->k; $index++){
			$hash_sub = hexdec(unpack('H*',substr($hash,$index*$this->m_chunk_size,$this->m_chunk_size))[1]) & $this->mask;
			if (!(ord($this->bit_array[$hash_sub >> 3]) & (1 << ($hash_sub % 8)))) return false;
		}
		return true;
	}
	public function unionWith($bf){
		if ($this->m != $bf->m) throw new Exception('Unable to merge due to vector difference.');
		if ($this->k != $bf->k) throw new Exception('Unable to merge due to hash count difference.');
		if ($this->hash != $bf->hash) throw new Exception('Unable to merge due to hash difference.');
		$this->n += $bf->n;
		for ($i = 0; $i < strlen($this->bit_array); $i++) $this->bit_array[$i] = chr(ord($this->bit_array[$i]) | ord($bf->bit_array[$i]));
	}
	public function intersectWith($bf){
		if ($this->m != $bf->m) throw new Exception('Unable to merge due to vector difference.');
		if ($this->k != $bf->k) throw new Exception('Unable to merge due to hash count difference.');
		if ($this->hash != $bf->hash) throw new Exception('Unable to merge due to hash difference.');
		$this->n -= $bf->n;
		for ($i = 0; $i < strlen($this->bit_array); $i++) $this->bit_array[$i] = chr(ord($this->bit_array[$i]) & ord($bf->bit_array[$i]));
	}
}
?>