<?php
/**
 * Created by PhpStorm.
 * User: Jason
 * Date: 2018/7/2
 * Time: 9:59
 */
namespace library\Service\Speech;

class HttpSpeech {

    /**
     * 文字转语音
     * @author Jason7 2018-06-30
     * @param  string $text 合成的文本，使用UTF-8编码，请注意文本长度必须小于1024字节
     * @param  string $cuid 用户唯一标识，用来区分用户，填写机器 MAC 地址或 IMEI 码，长度为60以内
     * @param  string $spd 语速，取值0-9，默认为5中语速
     * @param  string $pit 音调，取值0-9，默认为5中语调
     * @param  string $vol 音量，取值0-15，默认为5中音量
     * @param  string $per 发音人选择, 0为女声，1为男声，3为情感合成-度逍遥，4为情感合成-度丫丫，默认为普通女
     * @return audio
     */
    private static $text;

    private static  $cuid;

    private static  $spd;

    private static  $pit;

    private static  $vol;

    private static  $per;

    private static $instance;

    private $client;

    public function __construct() {
        self::$spd = env('BAIDU_VOICE_SPD',5);
        self::$pit = env('BAIDU_VOICE_PIT',5);
        self::$vol = env('BAIDU_VOICE_VOL',5);
        self::$per = env('BAIDU_VOICE_per',5);
        $this->client = new AipSpeech(env('BAIDU_VOICE_APP_ID'), env('BAIDU_VOICE_APIKEY'), env('BAIDU_SECRET_KEY'));
    }

    public static function getInstance(){
        if(empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function languageToSpeech($text){
        $result = $this->client->synthesis($text, 'zh', 1, array('per' => self::$per,'spd' => self::$spd,'pit' => self::$pit,'vol' => self::$vol, 'cuid'=> self::$cuid
        ));
        return $result;
    }
}