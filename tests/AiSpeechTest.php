<?php

namespace jingfengshi\BaiduVoice\Tests;

use jingfengshi\BaiduVoice\AipSpeech;
use jingfengshi\BaiduVoice\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;


class AiSpeechTest extends TestCase
{

    public function testGetContentFromVoiceInvalidType()
    {
        $w = new AipSpeech('mock-key','mock-key','mock');

        $this->expectException(InvalidArgumentException::class);

        $this->expectExceptionMessage('语音类型非法: mp3');

        $w->getContentFromVoice('test','mp3');

        $this->fail('Failed to assert getWeather throw exception with invalid argument.');

    }

}