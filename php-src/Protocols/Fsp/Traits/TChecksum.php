<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Traits;


/**
 * Trait TChecksum
 * @package kalanis\RemoteRequest\Protocols\Fsp\Traits
 * Process checksums

/-* assume that we have already zeroed checksum in packet - that means set it to zero - chr(0) *-/
unsigned int sum,checksum;
 unsigned char *t;
 for(t = packet_start, sum = 0; t < packet_end; sum += *t++);
 checksum= sum + (sum >> 8);

 * PHP, Perl
 $len = strlen($packet);
 $packet[1] = chr(0); // at first null checksum in packet
 $sum = 0; // or $len for client->server
 for ($i = 0; $i < $len; $i++) {
     $sum += ord($packet[$i]);
 }
 $checksum = ($sum + ($sum >> 8));
 $byteChecksum = $checksum & 0xff;
 */
trait TChecksum
{
    /*
     * Slower, can be understand and ported
     */
//    protected function computeCheckSum(): int
//    {
//        $data = $this->getChecksumPacket();
//        $len = strlen($data);
//        $sum = $this->getInitialSumChunk();
//        for ($i = 0; $i < $len; $i++) {
//            $sum += ord($data[$i]);
////            print_r(['chkcc', $i, ord($data[$i]), $sum]);
//        }
//        $checksum = ($sum + ($sum >> 8));
////        var_dump(['chks', $sum, decbin($sum), decbin($sum >> 8)]);
//        return $checksum & 0xff; // only byte
//    }

    /*
     * Faster, less intelligible
     */
    protected function computeCheckSum(): int
    {
        $sum = array_reduce(str_split($this->getChecksumPacket()), [$this, 'sumBytes'], $this->getInitialSumChunk());
        return ($sum + ($sum >> 8)) & 0xff;
    }

    abstract protected function getChecksumPacket(): string;

    abstract protected function getInitialSumChunk(): int;

    public function sumBytes(int $sum, string $char): int
    {
        return $sum + ord($char);
    }
}
