<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


/**
 * Class GetDir
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process Get Directory
// struct RDIRENT {
//     struct HEADER {
//         long  time;
//         long  size;
//         byte  type;
//     }
//     ASCIIZ name;
// }
// padding - round struct to 4 bytes block
 *
 * Idea for v3: Liquid headers - at first used bytes and then actual content; because it's necessary to encode long
 * strings (like multibyte unicode) or long numbers (like 64bit date); this remove necessity for rounding
 * Also need to send file rights and do not determine them extra - type-r-w-x
 * And maybe sending files one-by-one. Because long name in chinese fill the string name really fast. And it's better
 * for seeking on client side.
 */
class GetDir extends AAnswer
{
    protected GetDir\FileInfo $singleFile;
    protected int $position = 0;
    /** @var GetDir\FileInfo[] */
    protected array $files = [];

    protected function customInit(): void
    {
        parent::customInit();
        $this->singleFile = new GetDir\FileInfo('');
    }

    public function process(): parent
    {
        $this->position = $this->answer->getFilePosition();
        $data = $this->answer->getContent();
        $dataLen = $this->answer->getDataLength();
        // on begining up to 12 chars and check by last one against 0x00 (NULL byte), then add by 4; read ends with overflowing the body size
        $startSeq = 0;
        $endSeq = 0;
        $newPacket = true;
        do {
            if ($newPacket) {
                $endSeq =+ 12;
                $newPacket = false;
            }
            $record = substr($data, $startSeq, $endSeq);
            if (chr(0) == substr($record, -1, 1)) {
                $file = clone $this->singleFile;
                $this->files[] = $file->setData($record);

                $startSeq += $endSeq;
                $endSeq = 0;
                $newPacket = true;
            } else {
                $endSeq += 4;
            }
        } while ($startSeq < $dataLen);
        return $this;
    }

    /**
     * @return GetDir\FileInfo[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
