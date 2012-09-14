###Summary
This is a proper bloom filter implementation in PHP.  I could not find an space/time efficient open-source PHP one.
It uses a binary string to store the bit vector and manipulates based on byte indexes of the string.

###Performance
On a 3.8GHz Sandy Bridge system, the single threaded insert/lookup throughput is 100K/150K elements / second respectively with k = 6.

###Math Bits
* m vector bits
* k hash functions
* n elements
* p probability of false positive

* (1-(1-1/m)^(m*ln(2)))^(m*ln(2)/n)=p
* k = m*ln(2)/n;

###Notes and Limitations
* PHP Limitations
	* Strings are limited to byte addressing of signed 32 bit integers.  The maximum string is only 2GB - 1B (2^31-1 Bytes).
	* The bit vector only supports powers of 2 bits in this implementation.  Thus the largest vector size is 1GB.
	* Workaround with multiple strings could allow for implementations greater than 1GB.
	* Minor edits are required to support PHP 5.3 due to the use of array dereferencing features of PHP 5.4.
	* PHP lacks calloc or malloc so str_repeat is used to allocate the bit array.
	* PHP cannot directly use the output of str_repeat and primitive assignment will require double the memory of the vector size.

* MurmurHash is a hot topic in this realm.  It isn't implemented here though due to module dependency for fast implementation.
