###Summary
This is a proper bloom filter implementation in PHP.  I could not find an space/time efficient open-source PHP one.
It uses a binary string to store the bit vector and manipulates based on byte indexes of the string.

###Performance
On a 3.8GHz Sandy Bridge system, the single threaded lookup/insert throughput is 150K elements / second with k = 7.


###Math Bits
* m vector bits
* k hash functions
* n elements
* p probability of false positive

* (1-(1-1/m)^(m*ln(2)))^(m*ln(2)/n)=p
* k = m*ln(2)/n;



###Notes and Limitations
* The bit vector only supports powers of 2 bits in size.
* PHP 5.x variables are limited to byte addressing of signed 32 bit integers.  
	* The the maximum variable is only 2GB - 1B.
	* Thus the largest vector size is 1GB.
	* Workaround with multiple variables could the single vector size-limitation.
	* Minor edits are required to support PHP 5.3 due to the use of array dereferencing features of PHP 5.4.

* MurmurHash is a hot topic in this realm.  It isn't implemented here though due to module dependency for fast implementation.