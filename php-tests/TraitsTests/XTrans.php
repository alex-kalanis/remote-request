<?php

namespace tests\TraitsTests;


use kalanis\RemoteRequest\Interfaces\IRRTranslations;


class XTrans implements IRRTranslations
{
    public function rrPointUnknownTarget(): string
    {
        return 'mock';
    }

    public function rrPointNoStreamPointer(): string
    {
        return 'mock';
    }

    public function rrPointSentProblem(string $error): string
    {
        return 'mock';
    }

    public function rrPointReceivedProblem(string $error): string
    {
        return 'mock';
    }

    public function rrSchemaUnknownPacketWrapper(): string
    {
        return 'mock';
    }

    public function rrSchemaUnknownResponse(string $schema): string
    {
        return 'mock';
    }

    public function rrSocketCannotConnect(): string
    {
        return 'mock';
    }

    public function rrSocketCannotConnect2(string $errorMessage): string
    {
        return 'mock';
    }

    public function rrHelpInvalidLink(string $link): string
    {
        return 'mock';
    }

    public function rrHelpInvalidProtocolSchema(string $schema): string
    {
        return 'mock';
    }

    public function rrHelpInvalidRequestSchema(string $schema): string
    {
        return 'mock';
    }

    public function rrHelpInvalidResponseSchema(string $schema): string
    {
        return 'mock';
    }

    public function rrHttpAnswerHeaderTooLarge(int $wantedSize, int $gotSize): string
    {
        return 'mock';
    }

    public function rrFspResponseShort(int $size): string
    {
        return 'mock';
    }

    public function rrFspResponseLarge(int $size): string
    {
        return 'mock';
    }

    public function rrFspInvalidChecksum(int $expected, int $got): string
    {
        return 'mock';
    }

    public function rrFspEmptySequence(): string
    {
        return 'mock';
    }

    public function rrFspEmptyHost(): string
    {
        return 'mock';
    }

    public function rrFspNoAction(): string
    {
        return 'mock';
    }

    public function rrFspNoTarget(): string
    {
        return 'mock';
    }

    public function rrFspWrongSequence(int $sequence, int $key): string
    {
        return 'mock';
    }

    public function rrFspWrapMalformedPath(string $path): string
    {
        return 'mock';
    }

    public function rrFspBadResponseClose(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadResponseRead(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadResponseUpload(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadResponsePublish(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadResponseUnlink(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadMkDir(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadProtection(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadRename(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadRmDir(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadFileMode(string $class): string
    {
        return 'mock';
    }

    public function rrFspBadParsedPath(string $path): string
    {
        return 'mock';
    }

    public function rrFspPathNotFound(string $path): string
    {
        return 'mock';
    }

    public function rrFspFileCannotWrite(): string
    {
        return 'mock';
    }

    public function rrFspFileCannotCont(): string
    {
        return 'mock';
    }

    public function rrFspReadWrongSeek(int $wanted, int $got): string
    {
        return 'mock';
    }

    public function rrFspWriteWrongSeek(int $wanted, int $got): string
    {
        return 'mock';
    }
}
