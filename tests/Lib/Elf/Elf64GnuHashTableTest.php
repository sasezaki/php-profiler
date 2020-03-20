<?php

/**
 * This file is part of the sj-i/php-profiler package.
 *
 * (c) sji <sji@sj-i.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpProfiler\Lib\Elf;


use PHPUnit\Framework\TestCase;

/**
 * Class Elf64GnuHashTableTest
 * @package PhpProfiler\Lib\Elf
 */
class Elf64GnuHashTableTest extends TestCase
{

    public function testHash()
    {
        $this->assertSame(0x00001505, Elf64GnuHashTable::hash(''));
        $this->assertSame(0x156b2bb8, Elf64GnuHashTable::hash('printf'));
        $this->assertSame(0x7c967e3f, Elf64GnuHashTable::hash('exit'));
        $this->assertSame(0xbac212a0, Elf64GnuHashTable::hash('syscall'));
    }
}
