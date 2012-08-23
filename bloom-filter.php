<pre>
<?php
/*


https://github.com/Vaizard/MurmurHash3PHP

m bits
k hashes
n values
p probability of false positive 

//(1-(1-1/m)^(m*ln(2)))^(m*ln(2)/n)=p
*/
class BloomFilter {
	const OPTIMIZE_FOR_MEMORY = 1; // smallest m
	const OPTIMIZE_FOR_HASHES = 2; // smallest k
	public static function createFromProbability($n, $p, $method = 0){
		if ($p <= 0 || $p >= 1) throw new Exception('Invalid false positive rate requested.');
		if ($method){
			
		} else {
			$m = pow(2,ceil(log(-$n*log($p)/pow(log(2),2),2))); //approximate estimator method
			$k = round(log(1/$p,2));
		}
		return new BloomFilter($m,$k);
	}
	public static function union($bf1,$bf2){
		
	}
	public static function intersect($bf1,$bf2){
		
	}
	private $n = 0; // # of entries
	private $m; // # of bits in array
	private $k; // # of hash functions
	private $mask;
	private $m_chunk_size;
	private $bit_array;
	public function __construct($m,$k){
		if ($m < 8) throw new Exception('For practical applications, we restrict the bit array length to at least 8 bits.');
		if ($m & ($m - 1) == 0) throw new Exception('The bit array must be power of 2.');
		$this->m = $m; //number of bits
		$this->k = $k;
		$address_bits = log($m,2);
		$this->mask =(1 << $address_bits) - 1;
		$this->m_chunk_size = ceil($address_bits / 8);
		$this->bit_array = (binary)str_repeat("\0",ceil($this->m/8));
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
		$M = $this->m / 8;
		$magnitude = floor(log($M,1024));
		$unit = $units[$magnitude];
		$M /= pow(1024,$magnitude);
		echo 'Allocated m: '.$this->m.' bits ('.$M.' '.$unit.'Bytes)'.PHP_EOL;
		echo 'Allocated k: '.$this->k.PHP_EOL;
		if (isset($p)) echo 'Capacity ('.$p.'): '.number_format($this->calculateCapacity($p)).PHP_EOL;
	}
	public function add($key){
		$hash = md5($key,true);
		while ($this->m_chunk_size * $this->k > strlen($hash)) $hash .= md5($hash);
		
		for ($index = 0; $index < $this->k; $index++){
			$hash_index = $index * $this->m_chunk_size;
			$hash_sub = substr($hash,$hash_index,$this->m_chunk_size);
			
			echo 'Bit Mask:'.decbin($this->mask).PHP_EOL;
			echo 'Bit Array Memory Size: '.strlen($this->bit_array).PHP_EOL;
			echo 'Bit Array: '.bin2hex($this->bit_array).PHP_EOL;
			echo 'Hash: '.unpack('H*',$hash)[1].PHP_EOL;
			
			$hash = hexdec(unpack('H*',substr($hash,-1,1))[1]);
			var_dump($hash,$mask,$hash & $mask);
			echo 'INPUT:'.unpack('H*',$hash)[1].PHP_EOL;
			echo 'OUTPUT:'.unpack('H*',$hash & $mask)[1].PHP_EOL;
			
			
			
			echo 'Setting Bit '.''.' for Hash '.$index.PHP_EOL;
		}
	}
	public function check($key){
		$hash = md5($key,true);
	}
}
$bf1 = BloomFilter::createFromProbability(20, 0.01);
echo $bf1->getInfo(0.01);
//echo $bf1->calculateProbability(26).PHP_EOL;
$bf1->add('Test');
?>
</pre>