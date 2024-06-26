<?php

namespace kalanis\RemoteRequest\Protocols\Fsp\Answer;


use kalanis\RemoteRequest\Protocols\Fsp;


/**
 * Class Protection
 * @package kalanis\RemoteRequest\Protocols\Fsp\Answer
 * Process protection answer
 */
class Protection extends AAnswer
{
    protected string $directory = '';
    protected ?bool $isOwnedByMe = null;
    protected ?bool $canListDir = null;
    protected ?bool $canReadOnlyOwner = null;
    protected ?bool $canCreateFileHere = null;
    protected ?bool $canRenameFileHere = null;
    protected ?bool $canDeleteFileHere = null;
    protected ?bool $canCreateDirHere = null;
    protected ?bool $containsReadme = null;

    public function process(): parent
    {
        $this->directory = substr($this->answer->getContent(), 0, -1);
        $extraData = $this->answer->getExtraData();
        $settings = Fsp\Strings::mb_ord($extraData[0]);
        $this->isOwnedByMe = $this->parseBit($settings, 0x01);
        $this->canDeleteFileHere = $this->parseBit($settings, 0x02);
        $this->canCreateFileHere = $this->parseBit($settings, 0x04);
        $this->canCreateDirHere = $this->parseBit($settings, 0x08);
        $this->canReadOnlyOwner = $this->parseBit($settings, 0x10);
        $this->containsReadme = $this->parseBit($settings, 0x20);
        $this->canListDir = $this->parseBit($settings, 0x40);
        $this->canRenameFileHere = $this->parseBit($settings, 0x80);
        return $this;
    }

    /**
     * @param int $input
     * @param int $bitPosition
     * @return bool
     */
    protected function parseBit($input, $bitPosition): bool
    {
        return boolval($input & $bitPosition);
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function isMy(): ?bool
    {
        return $this->isOwnedByMe;
    }

    public function canList(): ?bool
    {
        return $this->canListDir;
    }

    public function canReadOnlyOwner(): ?bool
    {
        return $this->canReadOnlyOwner;
    }

    public function canCreateFile(): ?bool
    {
        return $this->canCreateFileHere;
    }

    public function canRenameFile(): ?bool
    {
        return $this->canRenameFileHere;
    }

    public function canDeleteFile(): ?bool
    {
        return $this->canDeleteFileHere;
    }

    public function canCreateDir(): ?bool
    {
        return $this->canCreateDirHere;
    }

    public function containsReadme(): ?bool
    {
        return $this->containsReadme;
    }
}
