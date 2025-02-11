<?php
/**
 * Created by PhpStorm.
 * User: wangju
 * Date: 2019-05-17
 * Time: 20:38
 */

namespace DingNotice;


use GuzzleHttp\Client;

class HttpClient implements SendClient
{
    protected $client;
    protected $config;
    /**
     * @var string
     */
    protected $hookUrl = "https://oapi.dingtalk.com/robot/send";

    /**
     * @var string
     */
    protected $accessToken = "";

    public function __construct($config)
    {
        $this->config = $config;
        $this->setAccessToken();
        $this->client = $this->createClient();
    }

    /**
     *
     */
    public function setAccessToken(){
        $this->accessToken = $this->config['token'];
    }

    /**
     * create a guzzle client
     * @return Client
     * @author wangju 2019-05-17 20:25
     */
    protected function createClient()
    {
        $client = new Client([
            'timeout' => $this->config['timeout'] ?? 2.0,
        ]);
        return $client;
    }

    /**
     * @return string
     */
    public function getRobotUrl(){
        return $this->hookUrl . "?access_token={$this->accessToken}";
    }

    /**
     * send message
     * @param $url
     * @param $params
     * @return array
     * @author wangju 2019-05-17 20:48
     */
    public function send($params): array
    {
        $request = $this->client->post($this->getRobotUrl(), [
            'body' => json_encode($params),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'verify' => $this->config['ssl_verify'] ?? true,
        ]);

        $result = $request->getBody()->getContents();
        return json_decode($result, true);
    }
}
