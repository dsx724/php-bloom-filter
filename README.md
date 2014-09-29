[blog](http://www.xuetech.com/search/label/Bloom%20Filter)
======

php-bloom-filter
================
This is a fast (possibly the fastest?) single threaded bloom filter implementation in pure PHP.
There are no dependencies on external modules unlike many other implementations.
It uses a binary string to store the bit vector and manipulates based on byte indexes of the string.
-[Apache 2.0 License](https://raw.github.com/dsx724/console-qrcode/master/LICENSE).

There are 3 implementations with slightly different performance characteristics:
* a 1GB vector implementation with (8-40 bit addressing) with any hash: 200K / 300K
* a 512MB vector implementation (32 bit addressing) with MD5: 240K / 400K
* a 64KB vector implementation (16 bit addressing) with MD5: 330K / 600K

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
On a 4.4GHz G3258 system, the single threaded insert/lookup throughput is 240K/400K elements / second respectively with six bits per key (k = 6).

It is faster than the following implementations by a significant margin:

https://github.com/mrspartak/php.bloom.filter
https://code.google.com/p/php-bloom-filter/	
https://packagist.org/packages/pleonasm/bloom-filter

cautionary tales
================
* PHP Limitations
	* Strings are limited to byte addressing of signed 32 bit integers.  The maximum string is only 2GB - 1B (2^31-1 Bytes).
	* The bit vector only supports powers of 2 bits in this implementation.  Thus the largest vector size is 1GB.
	* Workaround with multiple strings could allow for implementations greater than 1GB.
	* PHP 5.4+
	* PHP lacks calloc or malloc so str_repeat is used to allocate the bit array.
	* PHP cannot directly use the output of str_repeat and primitive assignment will require double the memory of the vector size due to the copy.
