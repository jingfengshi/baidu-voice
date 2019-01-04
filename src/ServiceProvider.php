<?php

namespace Jingfengshi\BaiduVoice;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    protected $defer = true;

    public function register()
    {
        $this->app->singleton(AipSpeech::class, function(){
            return new AipSpeech(config('services.aiSpeech.appid'),config('services.aiSpeech.appKey'),config('services.aiSpeech.appSecret'));
        });

        $this->app->alias(AipSpeech::class, 'AiSpeech');
    }

    public function provides()
    {
        return [AipSpeech::class, 'AiSpeech'];
    }
}