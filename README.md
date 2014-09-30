[blog](http://www.xuetech.com/search/label/Bloom%20Filter)
======

php-bloom-filter
================
* This is a fast (possibly the fastest?) single threaded bloom filter implementation in pure PHP.
* There are no dependencies on external modules unlike many other implementations.
* It uses a binary string to store the bit vector and manipulates based on byte indexes of the string.
* [Apache 2.0 License](https://raw.github.com/dsx724/console-qrcode/master/LICENSE).

There are 3 implementations with slightly different performance characteristics:
* a 1GB vector implementation with (8-40 bit addressing) with any hash.
* a 512MB vector implementation (32 bit addressing) with MD5.
* a 64KB vector implementation (16 bit addressing) with MD5.

math
====
* m vector bits
* k hash functions
* n elements
* p probability of false positive

* (1-(1-1/m)^(m*ln(2)))^(m*ln(2)/n)=p
* k = m*ln(2)/n;

performance
===========
On a 4.4GHz G3258 system, the single threaded insert/lookup throughput in elements per second respectively with six bit sets per key (k = 6):

On PHP 5.5
* a 1GB vector implementation with (8-40 bit addressing) with any hash: 209K / 329K
* a 512MB vector implementation (32 bit addressing) with MD5: 255K / 407K
* a 64KB vector implementation (16 bit addressing) with MD5: 347K / 602K

On HHVM:
* a 1GB vector implementation with (8-40 bit addressing) with any hash: 386K / 608K
* a 512MB vector implementation (32 bit addressing) with MD5: 510K / 874K
* a 64KB vector implementation (16 bit addressing) with MD5: 429K / 675K

It is faster than the following implementations by a significant margin:

*https://github.com/mrspartak/php.bloom.filter
*https://code.google.com/p/php-bloom-filter/	
*https://packagist.org/packages/pleonasm/bloom-filter

MD5 hashing is one of the more expensive operations and PHP does not have a native implementation of xxHash or Murmurhash3.

cautionary tales
================
* PHP Limitations
	* Strings are limited to byte addressing of signed 32 bit integers.  The maximum string is only 2GB - 1B (2^31-1 Bytes).
	* The bit vector only supports powers of 2 bits in this implementation.  Thus the largest vector size is 1GB.
	* Workaround with multiple strings could allow for implementations greater than 1GB.
	* PHP 5.4+
	* PHP lacks calloc or malloc so str_repeat is used to allocate the bit array.
	* PHP cannot directly use the output of str_repeat and primitive assignment will require double the memory of the vector size due to the copy.
