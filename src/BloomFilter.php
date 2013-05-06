<?php
/*
Copyright (c) 2012, Da Xue
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this software
   must display the following acknowledgement:
   This product includes software developed by Da Xue.
4. The name of the author nor the names of its contributors may be used
   to endorse or promote products derived from this software without
   specific prior written permission.

THIS SOFTWARE IS PROVIDED BY DA XUE ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL DA XUE BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

/* https://github.com/dsx724/php-bloom-filter */

/**
 * Bloom Filter
 */
class BloomFilter
{
    /**
     * Number of bits in array.
     *
     * @var integer
     */
    private $bitCount;

    /**
     * Number of hashes.
     *
     * @var integer
     */
    private $hashCount;

    /**
     * Function name to be use when hashing.
     *
     * @var string
     */
    private $hashFunction;

    /**
     * Bit mask.
     *
     * @var integer
     */
    private $bitMask;

    /**
     * Number of bytes to push off hash to generate an address.
     *
     * @var integer
     */
    private $chunkSize;

    /**
     * Binary structure that contains the filter.
     *
     * @var binary
     */
    private $bitArray;

    /**
     * Initialize bloom filter.
     *
     * @param integer $capacity     Filter capacity in number of entries
     * @param float   $maxErrorRate Probability of false positives
     */
    public function __construct($capacity, $maxErrorRate)
    {
        if ($maxErrorRate <= 0 || $maxErrorRate >= 1) {
            throw new Exception('Invalid false positive rate requested.');
        }

        $this->hashCount = floor(log(1 / $maxErrorRate, 2));

        if ($capacity <= 0) {
            throw new Exception('Invalid capacity requested.');
        }

        //approximate estimator method
        $this->bitCount = pow(2, ceil(log(-$capacity * log($maxErrorRate) / pow(log(2), 2), 2)));

        if ($this->bitCount > (pow(2, 30) * 8)) {
            throw new Exception('The maximum bit array size is 1GB.');
        }

        $this->hashFunction = 'md5';
        $addressBits        = (int)log($this->bitCount, 2);
        $this->bitMask         = (1 << $addressBits) - 1;
        $this->chunkSize    = (int)ceil($addressBits / 8);
        $this->bitArray     = (binary)(str_repeat("\0", $this->getFilterSizeInBytes()));
    }

    /**
     * Retrieve bloom filter size in bytes.
     *
     * @return integer
     */
    public function getFilterSizeInBytes()
    {
        return $this->bitCount >> 3; // divide by 8 the fast way (bits to bytes)
    }

    /**
     * Add $key to bloom filter.
     *
     * @param string $key
     */
    public function add($key)
    {
        $hash = $this->generateHashForKey($key);

        for ($index = 0; $index < $this->hashCount; $index++) {
            $subHash = hexdec(unpack('H*', substr($hash, $index * $this->chunkSize, $this->chunkSize))[1]) & $this->bitMask;

            $word = $subHash >> 3;
            $this->bitArray[$word] = chr(ord($this->bitArray[$word]) | 1 << ($subHash % 8));
        }
    }

    /**
     * Check if $key exists in bloom filter.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function contains($key)
    {
        $hash = $this->generateHashForKey($key);

        for ($index = 0; $index < $this->hashCount; $index++) {
            $subHash = hexdec(unpack('H*', substr($hash, $index * $this->chunkSize, $this->chunkSize))[1]) & $this->bitMask;

            if ( ! (ord($this->bitArray[$subHash >> 3]) & (1 << ($subHash % 8)))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Generate hash with the proper size to be used in bloom filter.
     *
     * @param string $key
     *
     * @return binary
     */
    public function generateHashForKey($key)
    {
        $hash = hash($this->hashFunction, $key, true);

        while ($this->chunkSize * $this->hashCount > strlen($hash)) {
            $hash .= hash($this->hashFunction, $hash, true);
        }

        return $hash;
    }
}
