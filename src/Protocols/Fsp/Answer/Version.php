<?php

namespace RemoteRequest\Protocols\Fsp\Answer;


use RemoteRequest\Protocols\Fsp;


/**
 * Class Version
 * @package RemoteRequest\Protocols\Fsp\Answer
 * Process FSP version
 */
class Version extends AAnswer
{
    protected $version = '';
    protected $serverLogs = null;
    protected $readOnly = null;
    protected $reverseLookup = null;
    protected $privateMode = null;
    protected $thruControl = null;
    protected $acceptExtra = null;
    protected $thruMaxAllowed = 0;
    protected $thruMaxPayload = 0;

    public function process(): parent
    {
        $this->version = substr($this->answer->getContent(), 0, -1);
        $extraSize = $this->answer->getFilePosition();
        $extraData = $this->answer->getExtraData();
        if ($extraSize && $extraData) {
            $settings = Fsp\Strings::mb_ord($extraData[0]);
            $this->serverLogs = $this->parseBit($settings, 0b10000000);
            $this->readOnly = $this->parseBit($settings, 0b01000000);
            $this->reverseLookup = $this->parseBit($settings, 0b00100000);
            $this->privateMode = $this->parseBit($settings, 0b00010000);
            $this->thruControl = $this->parseBit($settings, 0b00001000);
            $this->acceptExtra = $this->parseBit($settings, 0b00000100);
            if ($this->thruControl) {
                $this->thruMaxAllowed = Fsp\Strings::mb_ord(substr($extraData, 1, 4));
                $this->thruMaxPayload = Fsp\Strings::mb_ord(substr($extraData, 5, 2));
            }
        }
        return $this;
    }

    protected function parseBit($input, $bitPosition): bool
    {
        return ($input & $bitPosition);
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function isServerLogging(): ?bool
    {
        return $this->serverLogs;
    }

    public function isReadOnly(): ?bool
    {
        return $this->readOnly;
    }

    public function wantReverseLookup(): ?bool
    {
        return $this->reverseLookup;
    }

    public function isPrivateMode(): ?bool
    {
        return $this->privateMode;
    }

    public function acceptsExtra(): ?bool
    {
        return $this->acceptExtra;
    }

    public function canThruControl(): ?bool
    {
        return $this->thruControl;
    }

    public function thruControlMaxAllowed(): int
    {
        return $this->thruMaxAllowed;
    }

    public function thruControlMaxPayload(): int
    {
        return $this->thruMaxPayload;
    }
}
