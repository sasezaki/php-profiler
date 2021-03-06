<?php

/**
 * This file is part of the sj-i/php-profiler package.
 *
 * (c) sji <sji@sj-i.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PhpProfiler\Lib\Elf\Structure\Elf64;

use PhpProfiler\Lib\UInt64;

/**
 * Class Elf64SectionHeaderEntry
 * @package PhpProfiler\Lib\Elf
 */
final class Elf64SectionHeaderEntry
{
    public const SHT_NULL = 0;
    public const SHT_PROGBITS = 1;
    public const SHT_SYMTAB = 2;
    public const SHT_STRTAB = 3;
    public const SHT_RELA = 4;
    public const SHT_HASH = 5;
    public const SHT_DYNAMIC = 6;
    public const SHT_NOTE = 7;
    public const SHT_NOBITS = 8;
    public const SHT_REL = 9;
    public const SHT_SHLIB = 10;
    public const SHT_DYNSYM = 11;
    public const SHT_INIT_ARRAY = 14;
    public const SHT_FINI_ARRAY = 15;
    public const SHT_PREINIT_ARRAY = 16;
    public const SHT_GROUP = 17;
    public const SHT_SYMTAB_SHNDX = 18;
    public const SHT_LOOS = 0x60000000;
    public const SHT_HIOS = 0x6fffffff;
    public const SHT_LOPROC = 0x70000000;
    public const SHT_HIPROC = 0x7fffffff;
    public const SHT_LOUSER = 0x80000000;
    public const SHT_HIUSER = 0xffffffff;

    public int $sh_name; // Elf64_Word
    public int $sh_type; // Elf64_Word
    public UInt64 $sh_flags; // Elf64_Xword
    public UInt64 $sh_addr; // Elf64_Addr
    public UInt64 $sh_offset; // Elf64_Off
    public UInt64 $sh_size; // Elf64_Xword
    public int $sh_link; // Elf64_Word
    public int $sh_info; // Elf64_Word
    public UInt64 $sh_addralign; // Elf64_Xword
    public UInt64 $sh_entsize; // Elf64_Xword

    /**
     * Elf64SectionHeaderEntry constructor.
     * @param int $sh_name
     * @param int $sh_type
     * @param UInt64 $sh_flags
     * @param UInt64 $sh_addr
     * @param UInt64 $sh_offset
     * @param UInt64 $sh_size
     * @param int $sh_link
     * @param int $sh_info
     * @param UInt64 $sh_addralign
     * @param UInt64 $sh_entsize
     */
    public function __construct(
        int $sh_name,
        int $sh_type,
        UInt64 $sh_flags,
        UInt64 $sh_addr,
        UInt64 $sh_offset,
        UInt64 $sh_size,
        int $sh_link,
        int $sh_info,
        UInt64 $sh_addralign,
        UInt64 $sh_entsize
    ) {
        $this->sh_name = $sh_name;
        $this->sh_type = $sh_type;
        $this->sh_flags = $sh_flags;
        $this->sh_addr = $sh_addr;
        $this->sh_offset = $sh_offset;
        $this->sh_size = $sh_size;
        $this->sh_link = $sh_link;
        $this->sh_info = $sh_info;
        $this->sh_addralign = $sh_addralign;
        $this->sh_entsize = $sh_entsize;
    }

    /**
     * @return bool
     */
    public function isSymbolTable(): bool
    {
        return $this->sh_type === self::SHT_SYMTAB;
    }

    /**
     * @return bool
     */
    public function isStringTable(): bool
    {
        return $this->sh_type === self::SHT_STRTAB;
    }
}
