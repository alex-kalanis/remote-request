<?php

namespace kalanis\RemoteRequest;


use kalanis\RemoteRequest\Interfaces;


/**
 * Class Translations
 * @package kalanis\RemoteRequest
 * Return translated quotes from backend
 * - necessary due many translation systems through web
 * For work extends this class and pass extension into your project
 */
class Translations implements Interfaces\IRRTranslations
{
    public function rrPointUnknownTarget(): string
    {
        return 'Unknown target data for request';
    }

    public function rrPointNoStreamPointer(): string
    {
        return 'No stream pointer defined';
    }

    public function rrPointSentProblem(string $error): string
    {
        return 'Send problem: ' . $error;
    }

    public function rrPointReceivedProblem(string $error): string
    {
        return 'Receive problem: ' . $error;
    }

    public function rrSchemaUnknownPacketWrapper(): string
    {
        return 'Unknown packet wrapper type';
    }

    public function rrSchemaUnknownResponse(string $schema): string
    {
        return 'Unknown response available for protocol schema ' . $schema;
    }

    public function rrSocketCannotConnect(): string
    {
        return 'Cannot establish connection';
    }

    public function rrSocketCannotConnect2(string $errorMessage): string
    {
        return 'Cannot establish connection: ' . $errorMessage;
    }

    public function rrHelpInvalidProtocolSchema(string $schema): string
    {
        return 'Unknown protocol schema for known schema ' . $schema;
    }

    public function rrHelpInvalidRequestSchema(string $schema): string
    {
        return 'Unknown request available for schema ' . $schema;
    }

    public function rrHelpInvalidResponseSchema(string $schema): string
    {
        return 'Unknown response available for schema ' . $schema;
    }

    public function rrFspResponseShort(int $size): string
    {
        return 'Response too short';
    }

    public function rrFspResponseLarge(int $size): string
    {
        return 'Response too large';
    }

    public function rrFspInvalidChecksum(int $expected, int $got): string
    {
        return 'Invalid checksum';
    }

    public function rrFspEmptySequence(): string
    {
        return 'Empty sequence!';
    }

    public function rrFspEmptyHost(): string
    {
        return 'Empty host!';
    }

    public function rrFspNoAction(): string
    {
        return 'No action set.';
    }

    public function rrFspNoTarget(): string
    {
        return 'No target.';
    }

    public function rrFspWrongSequence(int $sequence, int $key): string
    {
        return sprintf('Wrong sequence! Got %d want %d', $sequence, $key);
    }

    public function rrFspWrapMalformedPath(string $path): string
    {
        return 'Malformed path: ' . $path;
    }

    public function rrFspBadResponseClose(string $class): string
    {
        return 'Got something bad with close. Class ' . $class;
    }

    public function rrFspBadResponseRead(string $class): string
    {
        return 'Got something bad with reading. Class ' . $class;
    }

    public function rrFspBadResponseUpload(string $class): string
    {
        return 'Got something bad with uploading. Class ' . $class;
    }

    public function rrFspBadResponsePublish(string $class): string
    {
        return 'Got something bad with publishing. Class ' . $class;
    }

    public function rrFspBadResponseUnlink(string $class): string
    {
        return 'Got something bad with unlink. Class ' . $class;
    }

    public function rrFspBadMkDir(string $mode): string
    {
        return 'Got something bad with mkdir. Class ' . $mode;
    }

    public function rrFspBadProtection(string $mode): string
    {
        return 'Got something bad with setting protections. Class ' . $mode;
    }

    public function rrFspBadRename(string $mode): string
    {
        return 'Got something bad with rename. Class ' . $mode;
    }

    public function rrFspBadRmDir(string $mode): string
    {
        return 'Got something bad with rmdir. Class ' . $mode;
    }

    public function rrFspBadFileMode(string $mode): string
    {
        return 'Got problematic mode: ' . $mode;
    }

    public function rrFspBadParsedPath(string $path): string
    {
        return 'Bad parsed path: ' . $path;
    }

    public function rrFspPathNotFound(string $path): string
    {
        return 'FSP path not found: ' . $path;
    }

    public function rrFspFileCannotWrite(): string
    {
        return 'File not open for writing!';
    }

    public function rrFspFileCannotCont(): string
    {
        return 'No more';
    }

    public function rrFspReadWrongSeek(int $wanted, int $got): string
    {
        return sprintf('Bad read seek. Want %d got %d ', $wanted, $got);
    }

    public function rrFspWriteWrongSeek(int $wanted, int $got): string
    {
        return sprintf('Bad write seek. Want %d got %d ', $wanted, $got);
    }
}
