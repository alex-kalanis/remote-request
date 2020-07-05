<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

/**
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
 */
class GetDir extends AAnswer
{
    /** @var GetDir\FileInfo|null */
    protected $singleFile = null;
    /** @var int */
    protected $position = 0;
    /** @var GetDir\FileInfo[] */
    protected $files = [];

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