<?php

namespace kalanis\RemoteRequest\Interfaces;


/**
 * Interface IRRTranslations
 * @package kalanis\RemoteRequest\Interfaces
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
interface IRRTranslations
{
    public function rrPointUnknownTarget(): string;

    public function rrPointNoStreamPointer(): string;

    public function rrPointSentProblem(string $error): string;

    public function rrPointReceivedProblem(string $error): string;

    public function rrSchemaUnknownPacketWrapper(): string;

    public function rrSchemaUnknownResponse(string $schema): string;

    public function rrSocketCannotConnect(): string;

    public function rrSocketCannotConnect2(string $errorMessage): string;

    public function rrHelpInvalidProtocolSchema(string $schema): string;

    public function rrHelpInvalidRequestSchema(string $schema): string;

    public function rrHelpInvalidResponseSchema(string $schema): string;

    public function rrFspResponseShort(int $size): string;

    public function rrFspResponseLarge(int $size): string;

    public function rrFspInvalidChecksum(int $expected, int $got): string;

    public function rrFspEmptySequence(): string;

    public function rrFspEmptyHost(): string;

    public function rrFspNoAction(): string;

    public function rrFspNoTarget(): string;

    public function rrFspWrongSequence(int $sequence, int $key): string;

    public function rrFspWrapMalformedPath(string $path): string;

    public function rrFspBadResponseClose(string $class): string;

    public function rrFspBadResponseRead(string $class): string;

    public function rrFspBadResponseUpload(string $class): string;

    public function rrFspBadResponsePublish(string $class): string;

    public function rrFspBadResponseUnlink(string $class): string;

    public function rrFspBadMkDir(string $class): string;

    public function rrFspBadProtection(string $class): string;

    public function rrFspBadRename(string $class): string;

    public function rrFspBadRmDir(string $class): string;

    public function rrFspBadFileMode(string $class): string;

    public function rrFspBadParsedPath(string $path): string;

    public function rrFspPathNotFound(string $path): string;

    public function rrFspFileCannotWrite(): string;

    public function rrFspFileCannotCont(): string;

    public function rrFspReadWrongSeek(int $wanted, int $got): string;

    public function rrFspWriteWrongSeek(int $wanted, int $got): string;
}
