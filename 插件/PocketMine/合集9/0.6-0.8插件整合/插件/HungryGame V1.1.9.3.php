<?php
/*
__PocketMine Plugin__
name=HungerGame
description=
version=1.1.9.3
apiversion=11,12
author=zzx
class=HungerGame
*/

/*
--1.0--
  饥饿1.0完工
  修复一堆存在BUG
--1.1-1.1.9.3--
  加入背包托管
  修复一堆一堆的bug
  加了一个 = 号
  修复退出游戏不清空物品
  修复退出游戏不清空装备
  修复部分坐标问题
  增加游戏中禁止使用桶
  修复bug
*/

class HungerGame implements Plugin{
    private $path,$api,$conf,$roomconf,$hgworld="未设置";
	private $we_list=array(),$we="off",$setpl=null,$setbl=null,$setch=null;
	private $players,$room,$players_list,$roomtime,$xy,$us="";
	private $hosting=array(),$znz="off";
	
	public function __construct(ServerAPI $api, $server = false){
        $this->api  = $api;
		$this->server = ServerAPI::request();
    }
    public function __destruct(){}
    public function init(){
		console(FORMAT_BLUE."[INIT] 饥饿游戏开始加载。");
		$this->path = $this->api->plugin->configPath($this);
		$this->newplayer = $this->api->plugin->configPath($this)."Players/";
		if (!file_exists( $this->newplayer )) {mkdir ($this->newplayer, 0777);}
		new Config($this->path."Room.yml", CONFIG_YAML, array());
		$this->conf = new Config($this->path."Config.yml", CONFIG_YAML, array(
			"礼包发放"=>"关",
			"礼包执行run"=>null,
			"饥饿游戏大厅"=>"暂无地图",
			"hgkey"=>null)); 
		$this->chest_item = new Config($this->path."chest_item.yml", CONFIG_YAML, array()); 
		$this->roomconf = $this->api->plugin->readYAML($this->path ."Room.yml");
		
		//$this->api->addHandler("player.interact", array($this, "interact"),98);
		$this->api->addHandler("console.command", array($this, "ppcmd"),99);
		$this->api->addHandler('player.block.break', array($this, 'blockbreak'));
		$this->api->addHandler('player.block.place', array($this, 'blockplace'));
		$this->api->addHandler('player.block.touch', array($this, 'blocktouch'));
		$this->api->addHandler('tile.update', array($this, 'tile'));
		$this->api->addHandler("player.quit", array($this, "pquit"), 99);
		$this->api->addHandler("player.move", array($this, 'move'),99);
		$this->api->addHandler("player.connect", array($this, 'connect'),99);
		$this->api->addHandler("player.spawn", array($this, 'spawn'),99);
		//$this->api->addHandler("player.respawn", array($this, 'respawn'),99);
		$this->api->addHandler("entity.health.change", array($this, "healthchange"),99);
		$this->api->console->register('hg', '饥饿游戏', array($this, 'hgcmd'));
		$this->api->ban->cmdWhitelist("hg");
		$this->api->console->register('hgset', '饥饿游戏设置', array($this, 'hgsetcmd'));
		$this->api->console->alias("hs", "hgset");
		console(FORMAT_BLUE."[INIT] 饥饿游戏加载完成！");
		$this->hgload();
	} 
	public function hgload(){
		$this->hgworld=$this->conf->get("饥饿游戏大厅");
		$this->we_list[]=$this->hgworld;
		console("[HG] ".FORMAT_YELLOW."当前游戏大厅地图：".FORMAT_GREEN."".$this->hgworld);
		foreach($this->roomconf as $room=>$value){
			$this->players_list[$room]=array();
			$this->room[$room]="off";
			$world=$this->roomconf[$room]["游戏地图"];
			$this->we_list[]=$world;}
		$this->setcompass="on";
		if($this->znz=="on"){
		$this->compass($cd=2);//指南针模块
		}
		console("[HG] ".FORMAT_YELLOW."当前已有房间数据：".FORMAT_GREEN."".count($this->roomconf));	
		}
	public function tile($data){
		if($data->class === TILE_SIGN){
        $player = $data->data['creator'];
		if($this->api->ban->isOP($player) === true){
		$world=$data->level->getName();
        if($world == $this->hgworld){
			$text1 = $data->data['Text1'];
			$text2 = $data->data['Text2'];
			$text3 = $data->data['Text3'];
			$text4 = $data->data['Text4'];
			if($text1 == "buildroom"){
			if(!$text2==null){
			if(!$text3==null){$world=$text3;}
				$xyz="$data->x".","."$data->y".","."$data->z";
				$this->roomconf[$text2]["木牌坐标"]=$xyz;
				$this->roomconf[$text2]["游戏地图"]=$world;//默认游戏地图为大厅
				if($text4 >= 2 and $text4 <= 18){$max=$text4;}else{$max=10;}
				$this->roomconf[$text2]["最大玩家数"]=$max;
				$this->room[$text2]="off";
				$this->saveall();
				$this->t($room=$text2);
                $msg="[HG] 设置房间$text2 成功,游戏地图$world 最大人数$max";
				$this->chats($msg,$world=$this->hgworld);
				$this->we_list[]=$world;
				$this->players_list[$text2]=null;
				console("[HG] ".FORMAT_GREEN."木牌房间：$text2 创建成功，最大人数$max");	
				}
}}}}}
	public function blocktouch($data){
	    $user = $data['player']->username;
	    $player = $data['player'];
		if($data["target"]->getID() == 323 or $data["target"]->getID() == 68 or $data["target"]->getID() == 63){
		    $sign = $this->api->tile->get(new Position ($data["target"], false, false, $data["target"]->level));
			if (($sign instanceof Tile) and $sign->class === TILE_SIGN and $data['type'] !== "touch"){
			if($sign->data['Text1']=="buildroom" or $sign->data['Text1']=="饥饿游戏"){
			$room=$sign->data['Text2'];
			if(!$this->roomconf[$room] == null){
			$xy=$this->roomconf[$room]['木牌坐标'];
			$max=$this->roomconf[$room]['最大玩家数'];
            $xy= explode(",",$xy);
     	    $cp=count($this->players_list[$room]);
			if($sign->x == $xy[0] and $sign->y == $xy[1] and $sign->z == $xy[2] and 
                $data['target']->level->getName() == $this->hgworld){
			    $plist=$this->players_list[$room];
				if($this->room[$room]=="on"){$player->sendChat("[HG] 对不起，本房间饥饿游戏已开始");}else{
		        if(!$this->players[$user]==null){$player->sendChat("[HG] 对不起，你已加入房间".$this->players[$user] ."。");}else{
				if($cp >= $max){$player->sendChat("[HG] 对不起，本房间人数已满");}else{
		        if($player->gamemode == CREATIVE){$player->sendChat("[HG] 创造模式无法加入房间！");}else{
		        $this->players_list[$room][]=$user;
				$this->players[$user]=$room;
				$ap=count($this->players_list[$room]);
                $this->t($room);
		        $msg="[HG] $user 加入饥饿游戏$room 目前人数$ap";
		        $this->chats($msg,$world=$this->hgworld);
				$cp=count($this->players_list[$room]);
                if($cp == $max){
                $this->goroom($room);
  	            $msg="[HG] $room 人数到达MAX，即将开始游戏";
		        $this->chats($msg,$world=$this->hgworld);
				console("[HG] ".FORMAT_GREEN."房间$room 人数$max 开始了游戏");
				}}}}}}}}
			if($sign->data['Text1']=="饥饿游戏功能" and $sign->data['Text2']=='强制开始房间'){
			$room=$sign->data['Text3'];
			if($data['target']->level->getName() == $this->hgworld){
			if(!$this->roomconf[$room]==null){
		    if($this->room[$room]=="off"){
			if(count($this->players_list[$room])>=2){
			$this->roomtime[$room]= 0 ;
			$this->zbdjs($room);
			$msg="[HG] 执行强制开始$room";
			}else{$msg="[HG] 对不起，房间人数不足2人";}
			}else{$msg="[HG] 对不起该房间游戏已开始";}
			}else{$msg="[HG] 对不起，房间不存在";}
			$data["player"]->SendChat($msg);
			}}
			if($sign->data['Text1']=='饥饿游戏功能' and $sign->data['Text2']=="quit"){
			if($data['target']->level->getName() == $this->hgworld){
			$user = $data['player']->username;
			if(!$this->players[$user]==null){	
            if($this->room[$this->players[$user]]=="off"){
			$this->playerquit($player=$user);
			}else{$msg="[HG] 无法退出，你所在的房间已开始";
			$data["player"]->SendChat($msg);}
			}else{$msg="[HG] 你还没有加入房间的说";
			$data["player"]->SendChat($msg);}
			}
			}
			}}
		$item=$data['item']->getID();
		if($this->we=="on"){
		if(!$this->setch[$user]==null){
		$ch= explode("-",$this->setch[$user]);
		$room=$ch[0];
		$num=$ch[1];
		if($data["player"]->entity->level->getName() == $this->roomconf[$room]['游戏地图']){
		if($data["target"]->getID() == 54){
		if($item == 345){
		$numadd=$num+1;
		if($numadd >19){
		$this->setch[$user]=null;
		$data['player']->SendChat("[HG] 已到达最大房间箱子数，停止设置");
		return false;
		}
		$xyz=$data["target"]->x.",".$data["target"]->y.",".$data["target"]->z;
		$this->roomconf[$room]['chests'][$num."-chest"]=$xyz;
		$this->saveall();
		$data['player']->SendChat("[HG] 房间$room 箱子编号$num 坐标$xyz 记录成功 \n[HG] 下一个编号".$numadd);
		$this->setch[$user]=$room."-".$numadd;
		return false;
		}}}}
		}elseif($item == 256){
		return false;
		}
		}
	public function blockplace($data){
	    $user = $data['player']->username;
        if($this->we=="off"){
        if(in_array($data['target']->level->getName(),$this->we_list)){
        return false;
        }}else{
        if(in_array($data['target']->level->getName(),$this->we_list)){
        if($this->api->ban->isOP($user) === false){return false;}}}
		//羊毛35:5绿色，35:11蓝色，35:14红色
		$block=$data['item']->getID();
		$meta=$data["item"]->getMetadata();
		if(!$this->setpl[$user]==null){
		$pl= explode("-",$this->setpl[$user]);
		$room=$pl[0];
		$num=$pl[1];
		if($data["player"]->entity->level->getName() == $this->roomconf[$room]['游戏地图']){
		if($block==35 and $meta==5){
		$numadd=$num+1;
		if($numadd >19){
		$this->setpl[$user]=null;
		$data['player']->SendChat("[HG] 已到达最大房间人数，停止设置");
		return false;
		}
		$y = $data["target"]->y +1;
		$xyz=$data["target"]->x.",".$y.",".$data["target"]->z;
		$this->roomconf[$room]['players'][$num."-player"]=$xyz;
		$this->saveall();
		$data['player']->SendChat("[HG] 房间$room 玩家编号$num 坐标$xyz 记录成功 \n[HG] 下一个编号".$numadd);
		$this->setpl[$user]=$room."-".$numadd;
		return false;
		}}}
		
		if(!$this->setbl[$user]==null){
		$bl= explode("-",$this->setbl[$user]);
		$room=$bl[0];
		$num=$bl[1];
		if($data["player"]->entity->level->getName() == $this->roomconf[$room]['游戏地图']){
		if($block==35 and $meta==14){
		$numadd=$num+1;
		$y = $data["target"]->y +1;
		$xyz=$data["target"]->x.",".$y.",".$data["target"]->z;
		$this->roomconf[$room]['blocks'][$num."-block"]=$xyz;
		$data['player']->SendChat("[HG] 房间$room 物品编号$num 坐标$xyz 记录成功 \n [HG] 下一个编号".$numadd);
		$this->setbl[$user]=$room."-".$numadd;
		$this->saveall();
		}}}
		
	}
	public function blockbreak($data){
		$user = $data['player']->username;
		if(!$this->players[$user] == null){
		$room=$this->players[$user];
		if($this->room[$room]=="on"){
        if($data['target']->level->getName()==$this->roomconf[$room]["游戏地图"]){
		if($data["target"]->getID() == 35){
        $this->giveitem($data);
		return true;
        }}}}
        if($this->we=="off"){
        if(in_array($data['target']->level->getName(),$this->we_list)){
        return false;
        }}else{
        if(in_array($data['target']->level->getName(),$this->we_list)){
        if($this->api->ban->isOP($user) === false){return false;}}}
	}
	public function ppcmd($data){
			if($data["issuer"] instanceof Player) {
			$user =$data["issuer"]->username;
			if(!$this->players[$user] == null){
			$room=$this->players[$user];
			if($this->room[$room]=="on"){
			return false;
			}}}
	}
	public function healthchange($data){//HP改变
	    $player=$data["entity"]->player;
		if($player instanceof player){
		    $user=$player->username;
			$wo = $data['entity']->level->getName();
            if(in_array($wo,$this->we_list)){
			if($this->players[$user]==null){
			return false;
			}else{
			$room=$this->players[$user];
			if($this->room[$room]=="off"){
			return false;
			}else{
			if($this->roomtime[$room] <= 50){
			return false;}
	        $tehp=$player->entity->getHealth();//玩家HP
			if($tehp <= 0){
			$this->playerquit($player=$user);
			$ap=count($this->players_list[$room]);
			if($ap >= 2){
			$msg="[HG] $user 死亡离开了$room ,剩余玩家$ap";
			console($msg);
	        $this->chats($msg,$world=$this->roomconf[$room]["游戏地图"]);
            }else{
			$msg="[HG] $user 死亡离开了饥饿游戏$room";
			$this->chats($msg,$world=$this->roomconf[$room]["游戏地图"]);
			foreach($this->players_list[$room] as $player){
			$msg="[HG] $player 获得了$room 的胜利";		
			if($this->roomconf[$room]["游戏地图"] == $this->hgworld){
			$this->chats($msg,$world=$this->hgworld);}else{
			$this->chats($msg,$world=$this->roomconf[$room]["游戏地图"]);
			$this->chats($msg,$world=$this->hgworld);}
			console("[HG] ".FORMAT_GREEN."房间：$room 结束了游戏,$player 获得了$room 的胜利");	
			$this->playerquit($player);
			$this->winitem($player);//发放奖励
			}
			$this->players_list[$room]=array();
			$this->room[$room]="off";
			}
			return false;
			}}}}}}			
	public function move($data){//玩家移动
		$user=$data->name;
		if($this->pmove[$user]=="off"){
		$x=$data->x;
		$z=$data->z;
		$y=$data->y;
		$xy=explode(",",$this->xy[$user]);
		$x= abs($xy[0] - $x);
		$y= abs($xy[1] - $y);
		$z= abs($xy[2] - $z);
		$world=$xy[3];
		if($x >0.5 or $y > 0.5 or $z < 0.5){
		$xzy= new Position((int)$xy[0],(int)$xy[1],(int)$xy[2], $this->api->level->get($world));
		$player=$this->api->player->get($user);
		if($player instanceof player){
		$player->teleport($xzy);}
		return;
		}}
	}
	public function goroom($room){
	$this->roomtime[$room]=-10;//10秒后开始游戏
	$this->room[$room]="on";
	$this->api->console->run("time set 0");
	$this->api->schedule(20, array($this, "djs"),$room, false);
	}
	public function zbdjs($room){
	$time=$this->roomtime[$room];
	$msg=null;
	$ap=count($this->players_list[$room]);
	if($this->room[$room]== "off"and $ap >= 2){
	if($time == 0){
	$msg="[HG] 房间$room 准备-倒计时40秒，40秒后开始游戏";}
	if($time == 10){
	$msg="[HG] 30秒后开始游戏,大家尽快加入房间$room";}
	if($time == 20){
	$msg="[HG] 20秒后开始游戏,大家尽快加入房间$room";}
	if($time == 40){
	$this->goroom($room);
  	$msg="[HG] $room 人数$ap ，即将开始游戏";
    $this->chats($msg,$world=$this->hgworld);
    console("[HG] ".FORMAT_GREEN."房间$room 人数$ap 开始了游戏");}
	$this->roomtime[$room]++;
	if(!$msg == null){
	$this->chats($msg,$world=$this->hgworld);}
	$this->api->schedule(20, array($this, "zbdjs"),$room, false);
	}else{
	$this->roomtime[$room]=null;
	}
	
	}
	public function djs($room){//房间倒计时
	$time=$this->roomtime[$room];
	$msg=null;
	if($this->room[$room]== "on"){
	if($time == -10){
	$msg="[HG] 游戏将在10秒后开始TP所有玩家进入地图";}
	if($time == -5){
	$msg="[HG] 游戏将在5秒后开始TP所有玩家进入地图";}
	if($time == 0){
	$this->start($room);
	$msg="[HG] 現在是准备阶段15秒内大家都不可以行动！";}
	if($time == 3){
	$msg="[HG] 在地图中有箱子,里头有本轮游戏的道具";}
	if($time == 5){
	$msg="[HG] 羊毛可以破壞里头会随机生成道具";}
	if($time == 7){
	$msg="[HG] 提醒你们一句，有工作台可以合成物品";}
	if($time == 10){
	$this->tmove($room);
	$msg="[HG] 解除移动限制！！！40秒内你们不会受到攻击";}
	if($time == 30){
	$msg="[HG] 伤害保护还有30秒！！";}
	if($time == 30){
	$msg="[HG] 伤害保护还有20秒！！";}
	if($time == 40){
	$msg="[HG] 伤害保护还有10秒！！";}
	if($time == 50){
	$msg="[HG] 游戏正式开始了，游戏最大时间600秒";}
	if($time == 350){
	$msg="[HG] 饥饿游戏已进行5分钟了剩余时间5分钟！";}
	if($time == 590){
	$msg="[HG] 饥饿游戏剩余时间60秒！";}
	if($time == 650){
	$this->endroom($room);
	}
	if(!$msg == null){
	$this->playerschat($msg,$room);}
	$this->roomtime[$room]++;
	$this->api->schedule(20, array($this, "djs"),$room, false);
	}else{
	$this->roomtime[$room]=null;
	}
	}
	public function start($room){
	if($this->room[$room]=="on"){
	if(count($this->players_list[$room])>=2){
	$this->tpplayer($room);
	$this->reblock($room);
	$this->rechest($room);
	$msg="[HG] 饥饿游戏房间$room 已开始 ！";
	console("[HG] ".FORMAT_GREEN."房间：$room 开始了游戏");	
	$this->chats($msg,$world=$this->roomconf[$room]["游戏地图"]);
	}}
	}
	public function endroom($room){
	$this->tmove($room);
	foreach($this->players_list[$room] as $player){
			$this->playerquit($player);}
			
	$this->room[$room]="off";
	$this->players_list[$room]=array();
	$world=$this->roomconf[$room]["游戏地图"];
	$msg="[HG] 饥饿游戏房间$room 被终止了";
	console("[HG] ".FORMAT_GREEN."房间：$room 被终止了游戏");	
	$this->chats($msg,$world);
	$this->chats($msg,$world=$this->hgworld);
	}
	public function winitem($player){//未测试
	    if($this->conf->get("礼包发放")=="开"){
		$player=$this->api->player->get($player);
		$lists = new Config($this->path."give-item.yml", CONFIG_YAML, array());
		$item_list=$lists->get("give-item");
	    foreach($item_list as $int){
		$int= explode(",",$int);
        $player->addItem((int)$int[0],(int)$int[1],(int)$int[2]);
        $player->sendchat("[HG] 获得胜利奖励");
		}
		$cmd=$this->conf->get("礼包执行run");
		if($cmd !==null){
		$num=0;
		$run=explode("-",$cmd);
		foreach($run as $key){
		if($key[$num]=="player"){$output .=$player;
		}else{
		$output .=$key[$num];}
		$unm++;
		}
		$this->api->console->run($output);}
		}
	}
	public function giveitem($data){//破坏羊毛35:5绿色，35:11蓝色，35:14红色
		$lists = new Config($this->path."block-get.yml", CONFIG_YAML, array());
		$color="白色";
		$a=1;
		if($data["target"]->getMetadata() ==5){
	    $color="绿色";}
	    if($data["target"]->getMetadata() ==11){
	    $color="蓝色";}
	    if($data["target"]->getMetadata() ==14){
	    $color="红色";}
		$item_list=$lists->get($color);
	    $item=array_rand($item_list);
		$item=$item_list[$item];
		$int= explode(",",$item);
        $data["player"]->addItem((int)$int[0],(int)$int[1],(int)$int[2]);
        $data["player"]->sendchat("[HG] 恭喜你获得物品 $int[0]");
	}
	public function spawn($data){//玩家进入
	    $user=$data->username;
		$player =$this->api->player->get($user);
		if(!file_exists($this->newplayer."$user.yml")){
			$pp =new Config($this->newplayer."$user.yml", CONFIG_YAML, array(
				"玩家"=>$user,
				"胜点"=>0,
				"物品"=>null
			));
			$pp->save();
		}
		if($player instanceof Player ){
		$world = $player->level->getName();
		$pp =new Config($this->newplayer."$user.yml", CONFIG_YAML);
		if($pp->get("remove") == "on"){
		foreach($player->inventory as $slot => $item){
     	    if($item->getID() > 0){
		    $player->removeItem($item->getID(), $item->getMetadata(), $item->count);
		}}
		$air = BlockAPI::getItem(AIR,0,0);
        $player->armor = array($air, $air, $air, $air);
        $player->sendArmor($player);
		$pp->set("remove","off");
		$pp->save();
}
		if(isset($this->hosting[$user])){
		    $hosting=$this->hosting[$user];
    		foreach($hosting as $slot => $item){
			    if(is_array($item))
				$player->addItem($item[0], $item[1], $item[2]);
			}
			unset($this->hosting[$user]);
			}
		if(in_array($world,$this->we_list)){
			$this->api->schedule(40, array($this, "tphgworld"),$player=$user, false);
			}}
		  
	}
	public function connect($data){//玩家加入
		$user=$data->username;
		$this->players[$user]=null;
		$this->pmove[$user]="on";
		$this->setch[$user]=null;
		$this->setbl[$user]=null;
		$this->setpl[$user]=null;	
	}
	public function rechest($room){//设置地图箱子
	$chests=$this->roomconf[$room]['chests'];
	$world=$this->roomconf[$room]['游戏地图'];
	foreach($chests as $ch_list=>$xyz){
	$c=explode(",",$xyz);
	$tile = $this->api->tile->get(new Position($c[0],$c[1],$c[2],$this->api->level->get($world)));
	if($tile == false) continue;
	if($tile->class != TILE_CHEST) continue;
	$num=mt_rand(3,9);
    $it=$this->chest_item->get("chest_item");
	$slot_item=array_rand($it,$num);
	$key2=0;
	foreach($slot_item as $key=>$value){
    $value=$it[$slot_item[$key]];
	$slot = explode(",", $value);
	$item = $this->api->block->getItem($slot[0], $slot[1], $slot[2]);
	$key2++;
	$tile->setSlot($key2,$item);
	}}
	}
	public function reblock($room){//设置方块
		$xyz1=$this->roomconf[$room]["blocks"];
		$world=$this->roomconf[$room]["游戏地图"];
		$level = $this->api->level->get($world);
		$num=1;
		foreach($xyz1 as $key=>$value){
	    $xz=$xyz1[$num."-block"];
		$int=explode(",",$value);
	    $meta=0;
		$r=mt_rand(0,4);
		if($r==1){$meta=5;}
		if($r==2){$meta=11;}
		if($r==3){$meta=14;}
		$v3 = new Vector3((int)$int[0],(int)$int[1],(int)$int[2]);
		$newblock = new Item(35,$meta);
		$newblock = $newblock->getBlock();
		$level->setBlock($v3,$newblock);
		$num++;
	    }}
	public function tpplayer($room){//TP玩家进入游戏并清空物品
	    $pls=$this->players_list[$room];
		$xyz1=$this->roomconf[$room]["players"];
		$world=$this->roomconf[$room]["游戏地图"];
		$num=1;
		foreach($pls as $key){
	    $player=$this->api->player->get($key);
		$this->pmove[$key] = "off";
		if($player instanceof player){
        $this->remove($player);
		$xz=$xyz1[$num."-player"];
		$this->xy[$player->username]=$xz.",".$world;
	    $xy=explode(",",$xz);
		$xzy= new Position((int)$xy[0],(int)$xy[1],(int)$xy[2], $this->api->level->get($world));		
        $this->hosting[$key]=array();
		$player->sendChat("[HG] 背包托管ing");
        foreach($player->inventory as $slot => $item){
	    if($item->getID() > 0){
			$this->hosting[$key][]=array($item->getID(), $item->getMetadata(), $item->count);
		    $player->removeItem($item->getID(), $item->getMetadata(), $item->count);
			}}
		$player->teleport($xzy);
		$player->setSpawn($this->api->level->get($this->hgworld)->getSpawn());
		$num++;
	    }
	}}
	public function tphgworld($player){//TP到饥饿游戏大厅
        $player=$this->api->player->get($player);
		if($player instanceof Player){
		$player->teleport($this->api->level->get($this->hgworld)->getSpawn());
		$player->setSpawn($this->api->level->get($this->hgworld)->getSpawn());
	}}
	public function playerquit($player){//玩家退出房间
		if(!$this->players[$player]==null){
		$room=$this->players[$player];
		$pl=$this->api->player->get($player);
		$quit=array_search($player , $this->players_list[$room]);
		array_splice($this->players_list[$room], $quit, 1); 
		if($this->room[$room]=="on"){
		$this->remove($player);//清空物品
		$pl->entity->harm(1);
 	    $this->tphgworld($player);//传送到大厅
		}
		$pl->entity->setHealth(20, "respawn", true);
	    $this->players[$player]=null;
		$pl->SendChat("[HG] 离开房间$room");
		$this->t($room);
		}
		}
	public function tmove($room){//解除玩家限制
	foreach($this->players_list[$room] as $player){
	$this->pmove[$player]="on";
    }}
	public function remove($player){//清空玩家物品
		$player=$this->api->player->get($player);
		$user=$player->username;
        //$room=$this->players[$user];		
	    if($player instanceof Player){ 
        foreach($player->inventory as $slot => $item){
     	    if($item->getID() > 0){
		    $player->removeItem($item->getID(), $item->getMetadata(), $item->count);
		}}
    	if(isset($this->hosting[$user])){
		    foreach($this->hosting[$user] as $slot => $item){
			    $player->addItem($item[0], $item[1], $item[2]);
				}
			$player->sendChat("[HG] 背包托管归还");
			unset($this->hosting[$user]);
		}
		$air = BlockAPI::getItem(AIR,0,0);
        $player->armor = array($air, $air, $air, $air);
        $player->sendArmor($player);
	    $player->setSpawn($this->api->level->get($this->hgworld)->getSpawn());
		//$player->sendInventory();
        //$player->sendChat("[HG] 你身上的道具已被清空");
		if($this->znz=="on"){
		$player->addItem(345,0,1);//给予指南针
		}
}}
	public function t($room){//刷新木牌
	    $xy=$this->roomconf[$room]['木牌坐标'];
		$max=$this->roomconf[$room]['最大玩家数'];
		$worldname = $this->api->level->get($this->hgworld);
		$xy= explode(",",$xy);
		$cp=count($this->players_list[$room]);
        $sign= $this->api->tile->get(new Position((int)$xy[0],(int)$xy[1],(int)$xy[2], $worldname));
		if(($sign instanceof Tile) and $sign->class === TILE_SIGN){
		$sign->setText("饥饿游戏",$room,"$cp / $max","状态 ：".$this->room[$room]);
}}
	public function hgcmd($cmd,$args,$data){
	switch($args[0]){
		case "":
		case "help":
		case "1":
			$output .="--HG帮助列表 1/1 页-- \n";
			$output .="/hg quit 退出目前房间 \n";
			$output .="/hg go [room] 强制开始房间 \n";
			$output .="/hg end [room] 强制结束房间";
			return $output;
		case "go":
		    $room=$args[1];
			if($this->api->ban->isOP($data->username) === false and $data!=="console"){return("[HG] 对不起，你没有权限使用它");}
			if(!$this->roomconf[$room]==null){
		    if($this->room[$room]=="off"){
			if(count($this->players_list[$room])>=2){
			$this->goroom($room);
			return("[HG] 执行强制开始$room");
			}else{return("[HG] 对不起，房间人数不足2人");}
			}else{return("[HG] 对不起该房间游戏已开始");}
			}else{return("[HG] 对不起，房间不存在");}
			break;
		case "end":
		    $room=$args[1];
			if($this->api->ban->isOP($data->username) === false and $data!=="console"){return("[HG] 对不起，你没有权限使用它");}
			if(!$this->roomconf[$room]==null){
		    if($this->room[$room]=="on"){
			$this->endroom($room);
			return("[HG] 执行强制结束$room");
			}else{return("[HG] 对不起该房间游戏未开始");}
			}else{return("[HG] 对不起，房间不存在");}
			break;
		case "quit":
		    if(!$this->players[$data->username]==null){
			$this->playerquit($player=$data->username);
			return("[HG] 成功退出房间");
			}else{return("[HG] 你还没有加入房间的说");}
			break;
		case "about":
			$output .=$this->us." 服务器HG  zx 授权于2014-12-14";
			return $output;
			default:
		    return("[HG] 请输入/hg help 插件帮助");
	}}
	public function hgsetcmd($cmd,$args,$data){
	switch($args[0]){
	    case "";
		case "1":
		case "help":
		    //$output .="---饥饿地图编辑设置--- \n";
			$output .="/hgset world [world] 设置大厅世界 \n";
			$output .="/hgset setworld 地图编辑启用/关闭 \n";
			$output .="/hgset players [room] [num] 玩家坐标编辑模式 \n";
			$output .="/hgset blocks [room] [num] 补给坐标编辑模式 \n";
			$output .="/hgset chest [room] [num] 选择箱子编辑模式 \n";
			$output .="/hgset max [room] [num] 设置房间最大人数";
			return $output;
		case "world":
		    $world=$args[1];
			if($world==null){return("[HG] /hgset world [world] 设置大厅世界");}
			$this->conf->set("饥饿游戏大厅",$world);
			$this->hgworld=$world;
			$this->we_list[]=$this->hgworld;
			$this->saveall();
			return("[HG] 设置饥饿游戏大厅地图为$world 成功");
        case "ch":
		case "chest":
		    $room=$args[1];
			$num=$args[2];
			if($data=='console'){return("[HG] 请在游戏中使用 ！");}
			$user=$data->username;
			if($num > 0 and $num <= 18){$num=$num;}else{$num=1;}
			if(!$this->roomconf[$room]==null){
			$this->setch[$user]=$room."-".$num;
			if($this->we=="off"){$output="[HG] 你还未启用地图编辑 \n";}
			return("$output"."[HG] 启用设置房间$room 编号$num 的箱子");
			}else{return("[HG] 对不起，房间不存在");}
		    break;
		case "pl":
		case "players":
			$room=$args[1];
			$num=$args[2];
			if($data=='console'){return("[HG] 请在游戏中使用 ！");}
			$user=$data->username;
			if($num > 0 and $unm <= 18){$num=$num;}else{$num=1;}
			if(!$this->roomconf[$room]==null){
			$this->setpl[$user]=$room."-".$num;
			if($this->we=="off"){$output="[HG] 你还未启用地图编辑 \n";}
			return("$output"."[HG] 启用设置房间$room 玩家编号$num 的坐标");
			}else{return("[HG] 对不起，房间不存在");}
		    break;
		case "bl":
		case "block":
			$room=$args[1];
			$num=$args[2];
			if($data=='console'){return("[HG] 请在游戏中使用 ！");}
			$user=$data->username;
			if($num > 0){$num=$num;}else{$num=1;}
			if(!$this->roomconf[$room]==null){
			$this->setbl[$user]=$room."-".$num;
			if($this->we=="off"){$output="[HG] 你还未启用地图编辑 \n";}
			return("$output"."[HG] 启用设置房间$room 物品编号$num 的坐标");
			}else{return("[HG] 对不起，房间不存在");}
		    break;
		case "we":
		case "setworld":
			if($this->we=="off"){
			$this->we="on";
			return("[HG] 地图编辑启用");}else{
			$this->we="off";
			return("[HG] 地图编辑关闭");}
			break;
		case "max":
			$room=$args[1];
			$max=$args[2];
			if(!$room==null and $max <= 18 and $max > 0){
			if(!$this->roomconf[$room]==null){
			$this->roomconf[$room]["最大玩家数"]=$max;
			$this->t($room);
			$this->saveall();
			return("[HG] 房间$room 最大人数为$max ");
			}else{return("[HG] 对不起，$room 不存在！");}
			}else{return("[HG] /hgset  max [room] [num] 设置房间最大人数");}
		default:
		    return("[HG] 请输入/hgset help 查看帮助");
					
	}
	}
	public function playerschat($msg,$room){//房间玩家msg
	    foreach($this->players_list[$room] as $player){
		$player=$this->api->player->get($player);
		if($player instanceof Player){
		$player->SendChat($msg);
		}}
	}
	public function compass($cd){
	if($this->setcompass == "on"){
	foreach($this->server->clients as $player){
	$player= $this->server->api->player->get($player);
	if($player instanceof Player){
	$room=$this->players[$player->username];
	$world = $player->level->getName();
	if(in_array($world,$this->we_list)){
	if($room !== null){
	if($this->room[$room]=="on"){
	$froms=99999;
	foreach($this->players_list[$room] as $rpl){
	if($rpl != $player->username){
	$rpl= $this->server->api->player->get($rpl);
	if($rpl instanceof Player){
	$x1=(int)$player->entity->x;
	$x2=(int)$rpl->entity->x;
	$y1=(int)$player->entity->z;
	$y2=(int)$rpl->entity->z;
	$from=(($x1 - $x2)*($x1 - $x2))+(($y1 - $y2)*($y1 - $y2));//计算距离
	if($from < $froms){
	$froms=$from;
	$min=$rpl;
	}}}}
	$rpl= $this->server->api->player->get($min);
	$xzy = new Position((int)$rpl->entity->x,(int)$rpl->entity->y,(int)$rpl->entity->z, $this->server->api->level->get($this->roomconf[$room]["游戏地图"]));
	$player->setSpawn($xzy);
	}}else{
	$player->setSpawn($this->server->api->level->get($this->hgworld)->getSpawn());}
	}}
	}
	$this->server->api->schedule($cd*20, array($this, "compass"),$cd, false);
	}}
	public function chats($msg,$world){//地图玩家msg
	foreach($this->server->clients as $player){
	$player= $this->api->player->get($player);
	if($player instanceof player){
    if($player->level->getName() == $world){
    $player->sendChat("$msg");
	}}}}
    public function saveall(){//保存数据
	$this->api->plugin->writeYAML($this->path."Room.yml", $this->roomconf);
    $this->conf->save();
	}
	public function pquit($data){//离开服务器
		$user = $data->username;
        if(isset($this->players[$user])){
		$player= $this->api->player->get($user);
		$room=$this->players[$user];
		$quit=array_search($user , $this->players_list[$room]);
		array_splice($this->players_list[$room], $quit, 1); 
		if($this->room[$room]=="on"){
		if(file_exists($this->newplayer."$user.yml")){
		$pp = new Config($this->newplayer."$user.yml", CONFIG_YAML);
		$pp->set("remove","on");
		$pp->save();
		}
		$ap=count($this->players_list[$room]);
		$msg="[HG] 玩家$user 离开了房间$room 目前剩余人数$ap";
		if($this->roomconf[$room]["游戏地图"] == $this->hgworld){
		$this->chats($msg,$world=$this->hgworld);}else{
		$this->chats($msg,$world=$this->roomconf[$room]["游戏地图"]);
		$this->chats($msg,$world=$this->hgworld);}
		$ap=count($this->players_list[$room]);
		if($ap < 2){
		$this->endroom($room);}
		}}else{
		$msg="[HG] 玩家$user 离开了服务器";
		$this->chats($msg,$world=$this->hgworld);}
		}
	
	
}
    
	
	