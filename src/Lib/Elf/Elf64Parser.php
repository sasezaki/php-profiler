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


use PhpProfiler\Lib\Binary\BinaryReader;

/**
 * Class Elf64Parser
 * @package PhpProfiler\Lib\Elf
 */
class Elf64Parser
{
    /**
     * @var BinaryReader
     */
    private BinaryReader $binary_reader;

    /**
     * Elf64Parser constructor.
     * @param BinaryReader $binary_reader
     */
    public function __construct(BinaryReader $binary_reader)
    {
        $this->binary_reader = $binary_reader;
    }

    /**
     * @param string $data
     * @return Elf64Header
     */
    public function parseElfHeader(string $data): Elf64Header
    {
        $header = new Elf64Header();
        $header->e_ident = [
            $this->binary_reader->read8($data, 0),
            $this->binary_reader->read8($data, 1),
            $this->binary_reader->read8($data, 2),
            $this->binary_reader->read8($data, 3),
            $this->binary_reader->read8($data, 4),
            $this->binary_reader->read8($data, 5),
            $this->binary_reader->read8($data, 6),
            $this->binary_reader->read8($data, 7),
            $this->binary_reader->read8($data, 8),
            $this->binary_reader->read8($data, 9),
        ];
        $header->e_type = $this->binary_reader->read16($data, 16);
        $header->e_machine = $this->binary_reader->read16($data, 18);
        $header->e_version = $this->binary_reader->read32($data, 20);
        $header->e_entry = $this->binary_reader->read64($data, 24);
        $header->e_phoff = $this->binary_reader->read64($data, 32);
        $header->e_shoff = $this->binary_reader->read64($data, 40);
        $header->e_flags = $this->binary_reader->read32($data, 48);
        $header->e_ehsize = $this->binary_reader->read16($data, 52);
        $header->e_phentsize = $this->binary_reader->read16($data, 54);
        $header->e_phnum = $this->binary_reader->read16($data, 56);
        $header->e_shentsize = $this->binary_reader->read16($data, 58);
        $header->e_shnum = $this->binary_reader->read16($data, 60);
        $header->e_shstrndx = $this->binary_reader->read16($data, 62);
        return $header;
    }

    /**
     * @param string $data
     * @param Elf64Header $elf_header
     * @return Elf64ProgramHeaderEntry[]
     */
    public function parseProgramHeader(string $data, Elf64Header $elf_header): Elf64ProgramHeaderTable
    {
        $program_header_table = [];

        for ($i = 0; $i < $elf_header->e_phnum; $i++) {
            $program_header = new Elf64ProgramHeaderEntry();
            // ToDo: handle 64 bit offset correctly
            $offset = $elf_header->e_phoff->lo + $elf_header->e_phentsize * $i;
            $program_header->p_type = $this->binary_reader->read32($data, $offset);
            $program_header->p_flags = $this->binary_reader->read32($data, $offset + 4);
            $program_header->p_offset = $this->binary_reader->read64($data, $offset + 8);
            $program_header->p_vaddr = $this->binary_reader->read64($data, $offset + 16);
            $program_header->p_paddr = $this->binary_reader->read64($data, $offset + 24);
            $program_header->p_filesz = $this->binary_reader->read64($data, $offset + 32);
            $program_header->p_memsz = $this->binary_reader->read64($data, $offset + 40);
            $program_header->p_align = $this->binary_reader->read64($data, $offset + 48);
            $program_header_table[] = $program_header;
        }

        return new Elf64ProgramHeaderTable(...$program_header_table);
    }

    /**
     * @param string $data
     * @param Elf64ProgramHeaderEntry $pt_dynamic
     * @return Elf64DynamicStructureArray
     */
    public function parseDynamicStructureArray(string $data, Elf64ProgramHeaderEntry $pt_dynamic): Elf64DynamicStructureArray
    {
        $dynamic_array = [];
        $offset = $pt_dynamic->p_offset->lo;
        do {
            $dynamic_structure = new Elf64DynamicStructure();
            $dynamic_structure->d_tag = $this->binary_reader->read64($data, $offset);
            $dynamic_structure->d_un = $this->binary_reader->read64($data, $offset + 8);
            $dynamic_array[] = $dynamic_structure;
            $offset += 16;
        } while (!$dynamic_structure->isEnd());

        return new Elf64DynamicStructureArray(...$dynamic_array);
    }

    /**
     * @param string $data
     * @param Elf64DynamicStructureArray $dynamic_structure_array
     * @return Elf64StringTable
     */
    public function parseStringTable(string $data, Elf64DynamicStructureArray $dynamic_structure_array): Elf64StringTable
    {
        /**
         * @var Elf64DynamicStructure $dt_strtab
         * @var Elf64DynamicStructure $dt_strsz
         */
        [
            Elf64DynamicStructure::DT_STRTAB => $dt_strtab,
            Elf64DynamicStructure::DT_STRSZ => $dt_strsz
        ] = $dynamic_structure_array->findStringTableEntries();
        $offset = $dt_strtab->d_un->toInt();
        $size = $dt_strsz->d_un->toInt();
        $string_table_region = substr($data, $offset, $size);
        $strings = explode("\0", $string_table_region);
        return new Elf64StringTable(...$strings);
    }
}