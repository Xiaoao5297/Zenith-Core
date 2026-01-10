<?php
/*
 __PocketMine Plugin__
name=Quitmsg
description=当玩家退出时提示所有人
version=0.0.1
author=MUedsa
class=Quitmsg
apiversion=12
*/

class Quitmsg implements Plugin
{
	private $api;

	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->server = ServerAPI::request();
	}

	public function init(){
        $this->api->addHandler("player.quit", array($this, "Quitmsg"), 5);
    }

	public function Quitmsg($data, $event){
		if ($event = "player.quit") {
			$msg = "[Quitmsg] " . $data->username . "退出了游戏";
			$this->server->api->chat->broadcast($msg);
		}
		return;
    }

    public function __destruct(){
	}

}