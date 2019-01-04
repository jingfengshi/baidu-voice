<?php
/*
* Copyright (c) 2017 Baidu.com, Inc. All Rights Reserved
*
* Licensed under the Apache License, Version 2.0 (the "License"); you may not
* use this file except in compliance with the License. You may obtain a copy of
* the License at
*
* Http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
* WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
* License for the specific language governing permissions and limitations under
* the License.
*/

namespace jingfengshi\BaiduVoice;

use jingfengshi\BaiduVoice\Exceptions\InvalidArgumentException;
use jingfengshi\BaiduVoice\lib\AipBase;

/**
 * 百度语音.
 */
class AipSpeech extends AipBase
{
    /**
     * url.
     *
     * @var string
     */
    public $asrUrl = 'http://vop.baidu.com/server_api';

    /**
     * url.
     *
     * @var string
     */
    public $ttsUrl = 'http://tsn.baidu.com/text2audio';

    /**
     * 判断认证是否有权限.
     *
     * @param array $authObj
     *
     * @return bool
     */
    protected function isPermission($authObj)
    {
        return true;
    }

    /**
     * 处理请求参数.
     *
     * @param string $url
     * @param array  $params
     * @param array  $data
     * @param array  $headers
     */
    protected function proccessRequest($url, &$params, &$data, $headers)
    {
        $token = isset($params['access_token']) ? $params['access_token'] : '';
        $data['cuid'] = md5($token);

        if ($url === $this->asrUrl) {
            $data['token'] = $token;
            $data = json_encode($data);
        } else {
            $data['tok'] = $token;
        }

        unset($params['access_token']);
    }

    /**
     * 格式化结果.
     *
     * @param $content string
     *
     * @return mixed
     */
    protected function proccessResult($content)
    {
        $obj = json_decode($content, true);

        if (null === $obj) {
            $obj = [
                'content' => $content,
            ];
        }

        return $obj;
    }


    /**
     * @param $speech
     * @param $format
     * @param int $rate
     * @param array $options
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getContentFromVoice($speech, $format, $rate=1600, $options = [])
    {
        $data = [];
        if(empty($speech)){
            throw new InvalidArgumentException('请输入语音内容');
        }
        if(!in_array(\strtolower($format),['pcm','wav','amr'])){
            throw new InvalidArgumentException('语音类型非法: '.$format);
        }

        try{
            $data['speech'] = base64_encode($speech);
            $data['len'] = strlen($speech);
            $data['format'] = $format;
            $data['rate'] = $rate;
            $data['channel'] = 1;

            $data = array_merge($data, $options);

            return $this->request($this->asrUrl, $data, []);
        }catch (\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode(), $e);
        }

    }

    /**
     * @param string $text
     * @param string $lang
     * @param int    $ctp
     * @param array  $options
     *
     * @return array
     */
    public function synthesis($text, $lang = 'zh', $ctp = 1, $options = [])
    {
        $data = [];

        $data['tex'] = $text;
        $data['lan'] = $lang;
        $data['ctp'] = $ctp;

        $data = array_merge($data, $options);

        $result = $this->request($this->ttsUrl, $data, []);

        if (!isset($result['err_no'])) {
            return $result['content'];
        }

        return $result;
    }
}
