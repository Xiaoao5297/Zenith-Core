<?php
 
/*
__PocketMine Plugin__
name=ItemLocker
description=
version=1.3
apiversion=12,13
author=RapDoodle
class=ItemLocker
*/
/*
当前插件作者是来自Pyramid编程小组的RapDoodle，根据国际开源条例，您可以对本插件进行任意的修改除了开头的注释部分（作者名称，插件名称）和一切ItemLocker的版权字样。
*/
 
class ItemLocker implements Plugin{
    private $api, $config;
 
    public function __construct(ServerAPI $api, $server = false){
        $this->api  = $api;
    }
     
    public function init(){
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array("high-permission" => false));
		if ($this->config->get("high-permission") === true){
			$this->api->addHandler("player.block.touch", array($this, "eventHandler"), 100);
			console("[INFO] ItemLocker高权限模式已启动");
		}else{
			$this->api->addHandler("player.block.touch", array($this, "eventHandler"), 8);
		}
		$this->api->console->register("item", "", array($this, "commandHandler"));
		$this->api->console->register("opitem", "", array($this, "commandHandler"));
		$this->api->console->alias("lock", "item lock");
		$this->api->console->alias("unlock", "item unlock");
		$this->api->console->alias("passlock", "item passlock");
		$this->api->console->alias("use", "item use");
		$this->api->ban->cmdWhitelist("item");
		$this->api->ban->cmdWhitelist("lock");
		$this->api->ban->cmdWhitelist("unlock");
		$this->api->ban->cmdWhitelist("passlock");
		$this->api->ban->cmdWhitelist("use");
    }
 
    public function __destruct(){}
	
	public function eventHandler(&$data){
		$itemID = $data['target']->getID();
		if (($itemID === 54) or ($itemID === 58) or ($itemID === 61) or ($itemID === 116) or ($itemID === 130) or ($itemID === 137) or ($itemID === 138) or ($itemID === 158)){
			$player = $data['player']->username;
			$x = $data['target']->x;
			$y = $data['target']->y;
			$z = $data['target']->z;
			$world = $data['player']->level->getName();
			$itemProfile = $this->getItem($x, $y, $z, $world, $itemID);
			if($itemProfile->exists($z)){
				$item = $itemProfile->get($z);
				$owner = $item ["Owner"];
				$on_lock = $item ["passmode"];
			}
			if (isset($this->locker[$player])){
				$eventCmd = $this->locker[$player];
				switch ($eventCmd[0]){
					case "use":
						$password = $eventCmd[1];
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							if ($on_lock === true){
								if ($password = $item ["passcode"]){
									$this->api->chat->sendTo(false, "[ItemLocker] 当前物品已经通过密码锁的形式打开了.", $player);
								}else{
									$this->api->chat->sendTo(false, "[ItemLocker] 密码错误.", $player);
									return false;
								}
							}else{
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品没有通过密码锁的形式锁定", $player);
								return false;
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品没有锁定", $player);
							return false;
						}
						break;
					case "op_use_item":
						if($itemProfile->exists($z)){
							if ($on_lock === true){
								$this->api->chat->sendTo(false, "[ItemLocker] 当前物品已经通过密码锁的形式锁定，锁定者".$owner.".", $player);
							}else{
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已被".$owner."锁定.", $player);
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品没有锁定", $player);
						}
						break;
					case "owner":
						unset($this->locker[$player]);
						$new_owner = $eventCmd[1];
						if($itemProfile->exists($z)){
							if ($owner !== $player){
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品属于".$owner."，您无权操作", $player); 
								return false;
							}else{
								if ($new_owner === NULL){
									$this->api->chat->sendTo(false, "[ItemLocker] 操作已取消：请输入目标修改拥有者的名字.", $player);
									return false;
								}else{
									$this->ChangeOwner($x, $y, $z, $world, $itemID, $new_owner);
									$this->api->chat->sendTo(false, "[ItemLocker] 已经修改该物品的拥有者为".$new_owner, $player);
									return false;
								}
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品没有被锁定", $player);
							return false;
						}
						break;
					case "opowner":
						unset($this->locker[$player]);
						$new_owner = $eventCmd[1];
						if($itemProfile->exists($z)){
							if ($new_owner === NULL){
								$this->api->chat->sendTo(false, "[ItemLocker] 操作已取消：请输入目标修改拥有者的名字.", $player);
								return false;
							}else{
								$this->ChangeOwner($x, $y, $z, $world, $itemID, $new_owner);
								$this->api->chat->sendTo(false, "[ItemLocker] 已经修改该物品的拥有者为".$new_owner, $player);
								return false;
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品没有被锁定", $player);
							return false;
						}
						break;
					case "info":
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							if ($on_lock === true){
								$this->api->chat->sendTo(false, "[ItemLocker] 当前物品已经通过密码锁的形式锁定了.锁定者：".$owner, $player);
								return false;
							}else{
								$this->api->chat->sendTo(false, "[ItemLocker] 锁定者：".$owner, $player);
								return false;
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品尚未锁定.", $player);
							return false;
						}
						break;
					case "lock":
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							if ($on_lock === true){
								$this->api->chat->sendTo(false, "[ItemLocker] 当前物品已经通过密码锁的形式锁定了.", $player);
								return false;
							}else{
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已被".$owner."锁定.", $player);
								return false;
							}
						}else{
							$passcode = NULL;
							$this->lock($x, $y, $z, $world, $itemID, $player, false, $passcode);
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已经锁定.", $player);
							if ($itemID === 54){
								$this->api->chat->sendTo(false, "[ItemLocker] 检测到您锁锁定的物品为箱子，为了安全起见：", $player);
								$this->api->chat->sendTo(false, "[ItemLocker] 请使用大箱子并且把两边锁上.", $player);
							}
							return false;
						}
						break;
					case "passlock":
						$password = $eventCmd[1];
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							if ($on_lock === true){
								$this->api->chat->sendTo(false, "[ItemLocker] 当前物品已经通过密码锁的形式锁定了.", $player);
								return false;
							}else{
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已被".$owner."锁定.", $player);
								return false;
							}
						}else{
							if ($password === NULL){
								$this->api->chat->sendTo(false, "[ItemLocker] 操作已取消：请输入需要设定的密码.", $player);
								return false;
							}else{
								$this->lock($x, $y, $z, $world, $itemID, $player, true, $password);
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已经通过密码锁锁定.", $player);
								if ($itemID === 54){
									$this->api->chat->sendTo(false, "[ItemLocker] 检测到您锁锁定的物品为箱子，为了安全起见：", $player);
									$this->api->chat->sendTo(false, "[ItemLocker] 请使用大箱子并且把两边锁上.", $player);
								}
								return false;
							}
						}
						break;
					case "unlock":
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							if ($owner !== $player){
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品属于".$owner."，您无权操作", $player); 
								return false;
							}else{
								$this->unlock($x, $y, $z, $world, $itemID, $player);
								$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已经解锁.", $player);
								return false;							
							}
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品暂未锁定.", $player);
							return false;
						}
						break;
					case "opunlock":
						unset($this->locker[$player]);
						if($itemProfile->exists($z)){
							$this->unlock($x, $y, $z, $world, $itemID, $player);
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已经强行解锁.", $player);
							return false;
						}else{
							$this->api->chat->sendTo(false, "[ItemLocker] 目标物品暂未锁定.", $player);
							return false;
						}
						break;
				}
			}elseif($itemProfile->exists($z) === true){
				if ($on_lock === true){
					$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已通过密码锁的方式锁定.", $player);
					return false;
				}elseif($owner !== $player){
					$this->api->chat->sendTo(false, "[ItemLocker] 目标物品已被".$owner."锁定.", $player);
					return false;
				}else{
					if ($on_lock === true and $data['type'] === 'break'){
						$this->api->chat->sendTo(false, "[ItemLocker] 请先解锁当前物品再破坏当前物品.", $player);
					}elseif ($owner === $player and $data['type'] === 'break'){
						$this->unlock($x, $y, $z, $world, $itemID, $player);
						$this->api->chat->sendTo(false, "[ItemLocker] 你已经解锁了目标物品通过破坏目标方块.", $player);
					}
				}
			}
		}
	}
	
	public function commandHandler($cmd, $args, $issuer, $alias){
		switch ($cmd){
			case "item":
				if($issuer === "console"){
					console("[ItemLocker] 不能在控台运行");
				}elseif($args[0] == "c" or $args[0] == "cancel"){
					unset($this->locker[$issuer->username]);
					$issuer->sendChat("[ItemLocker] 挂起状态已取消");
				}elseif(isset($this->locker[$issuer->username])){
					$issuer->sendChat("[ItemLocker] 您正处于命令挂起状态，请先完成上一次的任务。如需取消，请输入/item c");
				}else{
					$subCommand = $args[0];
					switch($subCommand){
						case "passlock":
							$password = $args[1];
							$this->locker[$issuer->username] = array('passlock', $password, );
							$issuer->sendChat("[ItemLocker] 请点击需要通过密码锁定的物品");
							break;
						case "lock":
							$this->locker[$issuer->username] = array('lock');
							$issuer->sendChat("[ItemLocker] 请点击需要锁定的物品");
							break;
						case "unlock":
							$this->locker[$issuer->username] = array('unlock');
							$issuer->sendChat("[ItemLocker] 请点击需要解锁的物品");
							break;
						case "info":
							$this->locker[$issuer->username] = array('info');
							$issuer->sendChat("[ItemLocker] 请点击需要获取信息的物品");
							break;
						case "use":
							$password = $args[1];
							$this->locker[$issuer->username] = array('use', $password, );
							$issuer->sendChat("[ItemLocker] 请点击需要通过密码使用的物品");
							break;
						case "owner":
							$new_owner = $args[1];
							$this->locker[$issuer->username] = array('owner', $new_owner, );
							$issuer->sendChat("[ItemLocker] 请点击需要修改拥有者的物品");
							break;
					}
				}
				break;
			case "opitem":
				if($issuer === "console"){
					console("[ItemLocker] 不能在控台运行");
				}elseif($args[0] == "c" or $args[0] == "cancel"){
					unset($this->locker[$issuer->username]);
					$issuer->sendChat("[ItemLocker] 挂起状态已取消");
				}elseif(isset($this->locker[$issuer->username])){
					$issuer->sendChat("[ItemLocker] 您正处于命令挂起状态，请先完成上一次的任务。如需取消，请输入/item c");
				}else{
					$subCommand = $args[0];
					switch($subCommand){
						case "owner":
							$new_owner = $args[1];
							$this->locker[$issuer->username] = array('opowner', $new_owner, );
							$issuer->sendChat("[ItemLocker] 请点击需要修改拥有者的物品");
							break;
						case "unlock":
							$this->locker[$issuer->username] = array('opunlock');
							$issuer->sendChat("[ItemLocker] 请点击需要强行解锁的物品");
							break;
						case "m":
						case "mode":
							switch($args[1]){
								case "on":
									$this->locker[$issuer->username] = array('op_use_item');
									$issuer->sendChat("[ItemLocker] 请点击需要以管理员模式查看或使用的物品");
									break;
								case "off":
									unset($this->locker[$issuer->username]);
									$issuer->sendChat("[ItemLocker] 管理员检查模式已经关闭");
									break;
							}
							break;
					}
				}
		}
	}
	
	public function getItem($x, $y, $z, $world, $itemID){
		@mkdir($this->api->plugin->configPath($this)."Items/");
		@mkdir($this->api->plugin->configPath($this)."Items/".$world."/");
		$ItemData = new Config($this->api->plugin->configPath($this)."Items/".$world."/".$x."_".$y.".yml");
		return $ItemData;
	}
	
	public function lock($x, $y, $z, $world, $itemID, $player, $passmode, $passcode){
		@mkdir($this->api->plugin->configPath($this)."Items/");
		@mkdir($this->api->plugin->configPath($this)."Items/".$world."/");
		$ItemData = new Config($this->api->plugin->configPath($this)."Items/".$world."/".$x."_".$y.".yml");
		if ($passmode === true){
			$ItemData->set($z, array("Owner" => $player, "ItemID" => $itemID, "passmode" => true, "passcode" => $passcode));
		}else{
			$ItemData->set($z, array("Owner" => $player, "ItemID" => $itemID, "passmode" => false, "passcode" => $passcode));
		}
		$ItemData->save();
	}
	
	public function ChangeOwner($x, $y, $z, $world, $itemID, $player){
		@mkdir($this->api->plugin->configPath($this)."Items/");
		@mkdir($this->api->plugin->configPath($this)."Items/".$world."/");
		$ItemData = new Config($this->api->plugin->configPath($this)."Items/".$world."/".$x."_".$y.".yml");
		$ItemData->set($z, array("Owner" => $player, "ItemID" => $itemID));
		$ItemData->save();
	}
	
	public function unlock($x, $y, $z, $world, $itemID, $player){
		@mkdir($this->api->plugin->configPath($this)."Items/");
		@mkdir($this->api->plugin->configPath($this)."Items/".$world."/");
		$ItemData = new Config($this->api->plugin->configPath($this)."Items/".$world."/".$x."_".$y.".yml");
		$ItemData->remove($z);
		$ItemData->save();
	}
}
?>