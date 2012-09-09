###Summary
This is a proper bloom filter implementation in PHP.  I could not find an space/time efficient open-source PHP one.
It uses a binary string to store the bit vector and manipulates based on byte indexes of the string.

###Performance
On a 3.8GHz Sandy Bridge system, the single threaded lookup/insert throughput is 150K elements / second with k = 7.


###Math Bits
m vector bits
k hash functions
n elements
p probability of false positive

(1-(1-1/m)^(m*ln(2)))^(m*ln(2)/n)=p
k = m*ln(2)/n;



###Notes and Limitations

* PHP 5.4 variables are limited to byte addressing from the signed 32 bit integer and thus the maximum variable is only 2GB - 1B.
	* Since this implementation require powers of 2 for filter vector size, the largest vector size is 1GB.
	* Workaround with multiple variables could overcome this limitation.
	*It uses array dereferencing features of PHP 5.4.  Only minor edits should be required to support PHP 5.3.

* MurmurHash is a hot topic in this realm.  It isn't implemented here though due to module dependency for fast implementation.