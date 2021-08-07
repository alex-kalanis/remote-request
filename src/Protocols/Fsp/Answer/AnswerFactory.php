<?php

namespace RemoteRequest\Protocols\Fsp\Answer;


use RemoteRequest\Protocols\Fsp;


/**
 * Class AnswerFactory
 * @package RemoteRequest\Protocols\Fsp\Answer
 * Factory for selecting correct answer - pythonic style
 */
class AnswerFactory
{
    public static function getObject(Fsp\Answer $answer): AAnswer
    {
        switch ($answer->getCommand()) {
            case Fsp::CC_VERSION:
                return new Version($answer);
            case Fsp::CC_GET_DIR:
                return new GetDir($answer);
            case Fsp::CC_GET_FILE:
            case Fsp::CC_GRAB_FILE:
                return new GetFile($answer);
            case Fsp::CC_UP_LOAD:
                return new Upload($answer);
            case Fsp::CC_GET_PRO:
            case Fsp::CC_SET_PRO:
            case Fsp::CC_MAKE_DIR:
                return new Protection($answer);
            case Fsp::CC_STAT:
                return new Stats($answer);
            case Fsp::CC_INSTALL:
            case Fsp::CC_DEL_FILE:
            case Fsp::CC_DEL_DIR:
            case Fsp::CC_BYE:
            case Fsp::CC_GRAB_DONE:
            case Fsp::CC_RENAME:
//            case Fsp::CC_CH_PASSW: // undefined
                return new Nothing($answer);
            case Fsp::CC_LIMIT: // reserved
            case Fsp::CC_TEST: // reserved
                return new Test($answer);
            case Fsp::CC_ERR:
            default:
                return new Error($answer);
        }
    }
}
