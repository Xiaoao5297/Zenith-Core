<?php
/*
 __PocketMine Plugin__
name=spawnmsg
description=当玩家进入时提示所有人
version=1
author=mougou
class=spawnmsg
apiversion=12
*/

class spawnmsg implements Plugin
{
	private $api;

	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$this->server = ServerAPI::request();
	}

	public function init(){
        $this->api->addHandler("player.spawn", array($this, "spawnmsg"), 5);
    }

	public function spawnmsg($data, $event){
		if ($event = "player.spawn") {
		$msg = "[spawnmsg] " . $data->username . "加入了游戏!";
			$this->server->api->chat->broadcast($msg);
		}
		return;
    }

    public function __destruct(){
	}

}