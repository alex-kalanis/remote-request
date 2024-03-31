<?php

namespace kalanis\RemoteRequest\Traits;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;
use kalanis\RemoteRequest\Translations;


/**
 * Trait TLang
 * @package kalanis\RemoteRequest\Processing
 * Translations trait
 */
trait TLang
{
    protected ?IRRTranslations $rrLang = null;

    public function setRRLang(?IRRTranslations $rrLang = null): void
    {
        $this->rrLang = $rrLang;
    }

    public function getRRLang(): IRRTranslations
    {
        if (empty($this->rrLang)) {
            $this->rrLang = new Translations();
        }
        return $this->rrLang;
    }
}
