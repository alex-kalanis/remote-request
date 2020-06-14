<?php

namespace RemoteRequest\Protocols\Fsp\Answer;

/**
 * Process Get Directory
 */
class GetDir extends AAnswer
{
    protected $position = 0;
    protected $listing = [];

    public function process(): parent
    {
        $this->position = $this->answer->getFilePosition();
        $this->listing = $this->parseStructure($this->answer->getContent());
        /*
            struct RDIRENT {
                struct HEADER {
                    long  time;
                    long  size;
                    byte  type;
                }
                ASCIIZ name;
            }
         */
        return $this;
    }

    protected function parseStructure(string $data): array
    {
        return [];
//        while (strlen($data) >= _DIRHEADER_LEN + 1) {
//            $dirent = RDirent($data);
//            $data = $data[$dirent.packed_size():];
//            yield $dirent;
//        }
    }
}