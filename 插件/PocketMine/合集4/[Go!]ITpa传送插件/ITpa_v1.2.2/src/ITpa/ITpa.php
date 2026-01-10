<?php

namespace ITpa;

use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

use pocketmine\utils\Config;          
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender; 
use onebone\economyapi\EconomyAPI;

use pocketmine\level\Position;        
use pocketmine\level\Level;
use pocketmine\math\Vector3;

use pocketmine\event\player\PlayerDeathEvent;   
use pocketmine\event\player\PlayerJoinEvent;     
use pocketmine\event\player\PlayerRespawnEvent;

/*
*  Sounth 声音
*/
use pocketmine\level\sound\EndermanTeleportSound;   //小黑的的声音
use pocketmine\level\sound\BlazeShootSound;         //射
use pocketmine\level\sound\TNTPrimeSound;           //TNT的声音
/*
*  Particle 粒子
*/
use pocketmine\level\particle\EnchantParticle;   //黑色药水圈圈
use pocketmine\level\particle\SplashParticle;     //水粒掉落
use pocketmine\level\particle\PortalParticle;      //末影圈圈
use pocketmine\level\particle\ExplodeParticle;    //燃烧白色烟雾
use pocketmine\level\particle\CriticalParticle;     //棕色
########################################
#   I传送插件
#   本插件开源禁止倒卖人人有责
#   作者 ibook 
#   @qq: 1542660471
#   bug反馈群(qq): 546488737
#   为了照顾新人已在难点加注释
########################################
class ITpa extends PluginBase implements Listener{
	
private $Player,$IConfig,$Vip;
    
	private function TpaSoundParticle($Ent,$t=1){
		$x=$Ent->getX();
		$y=$Ent->getY();
		$z=$Ent->getZ();
		$l=$Ent->getLevel();
		switch($t){
			case 1:
			    $l->addSound(new BlazeShootSound(new Vector3($x,$y,$z)));
				for($c=0;$c<2;$c++){
					$y++;
			    	$l->addParticle(new PortalParticle(new Vector3($x-1,$y,$z-1)));
				    $l->addParticle(new PortalParticle(new Vector3($x-1,$y,$z)));
				    $l->addParticle(new PortalParticle(new Vector3($x-1,$y,$z+1)));
				    $l->addParticle(new PortalParticle(new Vector3($x+1,$y,$z-1)));
				    $l->addParticle(new PortalParticle(new Vector3($x+1,$y,$z)));
				    $l->addParticle(new PortalParticle(new Vector3($x+1,$y,$z+1)));
				}
				    $y++;
			    	$l->addParticle(new SplashParticle(new Vector3($x-1,$y,$z-1)));
				    $l->addParticle(new SplashParticle(new Vector3($x-1,$y,$z)));
				    $l->addParticle(new SplashParticle(new Vector3($x-1,$y,$z+1)));
				    $l->addParticle(new SplashParticle(new Vector3($x+1,$y,$z-1)));
				    $l->addParticle(new SplashParticle(new Vector3($x+1,$y,$z)));
				    $l->addParticle(new SplashParticle(new Vector3($x+1,$y,$z+1)));
			    break;
			case 2:
				$l->addSound(new BlazeShootSound(new Vector3($x,$y,$z)));
				for($c=0;$c<3;$c++){
					$y++;
			    	$l->addParticle(new CriticalParticle(new Vector3($x-1,$y,$z-1)));
				    $l->addParticle(new CriticalParticle(new Vector3($x-1,$y,$z)));
				    $l->addParticle(new CriticalParticle(new Vector3($x-1,$y,$z+1)));
				    $l->addParticle(new CriticalParticle(new Vector3($x+1,$y,$z-1)));
				    $l->addParticle(new CriticalParticle(new Vector3($x+1,$y,$z)));
				    $l->addParticle(new CriticalParticle(new Vector3($x+1,$y,$z+1)));
					}
			    break;
			case 3:
				$l->addSound(new TNTPrimeSound(new Vector3($x,$y,$z)));
				$l->addParticle(new ExplodeParticle(new Vector3($x,$y--,$z)));
				$l->addParticle(new ExplodeParticle(new Vector3($x,$y++,$z)));
				$l->addParticle(new ExplodeParticle(new Vector3($x,$y++,$z)));
			    break;
		}
	}
	
    public function onEnable()
    {
		if(!is_dir($this->getDataFolder())){
		    $this->getLogger()->info(TextFormat::GOLD.'正在为第一次运行本程序做准备...');
			
			@mkdir($this->getDataFolder());        
			@mkdir($this->getDataFolder().'IData');
			
    	    $this->con = new Config($this->getDataFolder().'IConfig_配置文件.yml', Config::YAML,array(
	    	'全体传送' =>array(
			    '什么是全体传送?'=>'全体传送相当于你给全服所有在线玩家发送了[/传送到这|tpahere]的请求',
			    '怎么全体传送？'=>'全体传送就是指令 /全体传送|tpall',
				'全体可用'=>false,
				'OP  可用'=>true,
				'VIP 可用'=>false,
				'VIP+可用'=>true,
				'全体传送价格'=>200,
				),
			'传送付费' =>true,
			'传送价格' =>1,
	    	'安家付费' =>true,
	    	'安家价格' =>500,
			'版本检测'=>1,
			));
			$this->getLogger()->info(TextFormat::GOLD.'初始化完成欢迎使用本插件 ! ');
	    }else{
			$this->con = new Config($this->getDataFolder().'IConfig_配置文件.yml', Config::YAML);
		}
        
			$this->player  = new Config($this->getDataFolder().'IData/Player.yml', Config::YAML,array());
			$this->warp    = new Config($this->getDataFolder().'IData/Warp.yml', Config::YAML,array());
		
		if($this->con->get('版本检测')!=1){
			$this->getLogger()->info(TextFormat::RED.'[waring] 由于版本更新带来的数据结构更改请');
			$this->getLogger()->info(TextFormat::RED.'[waring] 删除IConfig_配置文件.yml文件否则无法正常使用本插件');
		}
		$this->getLogger()->info(TextFormat::GOLD.'如果发现什么BUG 可以加入 我会及时修复 ');
		
		$this->saveResource('Uplog.txt',true);
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::GREEN . 'ITpa v1.2.2 For API3.0.0+ 已安全启动');
		$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
		$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
	    
	}
	
	private function isOnline($n){
		$n = strtolower($n);
		foreach( $this->getServer()->getOnlinePlayers() as $P ){
			if(strtolower($P->getName())==$n){
				return true;
			}
		}return false;
	}
	
	/*
	Warp
	*/
	public function isWarp($warpname){
	    if($this->warp->get($warpname)==null)
			 return false;
		else
	         return true;
    } 
	
    public function onJoin(playerJoinEvent $event){
		$name = strtolower($event->getPlayer()->getName());
		if(!array_key_exists($name,$this->player->getAll())){
			$this->player->set($name,array(
			'Request'    =>null,
			'RequestType'=>null,
			'RequestTime'=>null,
			'Home'   =>null,
			'Back'   =>null,
			));
            $this->player->save();			
		}
	}
	
	
	
	public function onDeath(playerDeathEvent $event){
		/*判断事件发生者*/
		$Ent = $event->getEntity();
		$P = $Ent->getPlayer();
			$P = $Ent->getPlayer();
			$name = strtolower($P->getName());
        	$data = $this->player->get($name);
        	$data['Back']['X']=$P->getX();	
    	    $data['Back']['Y']=$P->getY();	
	        $data['Back']['Z']=$P->getZ();	
        	$data['Back']['L']=$P->getLevel()->getName();	
			$this->player->set($name,$data);
			$this->player->save();
	}
	
	public function onRespawn(playerRespawnEvent $event){
		$P = $event->getPlayer();
		//if($Ent instanceof Player){
	        $data = $this->player->get(strtolower($P->getName()));
			if($data['Back']!=null)
	       		$P->sendMessage('§4> §7你有死亡记录输入 §2/返回|back §7进行传送');
		//}
	}	
		
	public function onCommand(CommandSender $player, Command $cmd, $label, array $args)
	{  
	    $name = strtolower($player->getName());
	    $NAME = strtoupper($player->getName());
	  
	    IF(!($player instanceof Player)){
			$player->sendMessage('§4> §7请不要在控制台输入本指令');
	    }ELSE{
    	switch($cmd){
			case '设置地标':
			case 'setwarp':
			    ////IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    
				if(!$player->isOp()){
					$player->sendMessage('§4> §7只有管理员才可以使用这个指令');
				    break;}
				
				if(!isset($args[0])){
					$player->sendMessage('§4> §7格式错误, 缺少地标名, §2/setwarp|设置地标 地标名');
				    break;}
					
                $data = array('Level'=>$player->getLevel()->getName(),'X'=>floor($player->getX()),'Y'=>floor($player->getY()),'Z'=>floor($player->getZ()));
				$this->warp->set($args[0],$data);
				$this->warp->save();
				$this->TpaSoundParticle($player,2);
				$player->sendMessage('§4> §7地标设置成功! ');
				break;
			case '地标':
			case 'warp':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
				
				if(!isset($args[0])){
					$player->sendMessage('§4> §7格式错误, 缺少地标名, 格式§2/warp|设置地标 地标名|ID');
					$player->sendMessage('§4> §7请输入地标名, 输入§2/warplist|地标列表 §7来查看所有的地标');
				    break;}
					
				if($this->isWarp($args[0])==false){
					$player->sendMessage('§4> §7不存在这个地标');
					$player->sendMessage('§4> §7输入§2/warplist|地标列表 §7来查看所有的地标');
				    break;}
					
				$data = $this->warp->get($args[0]);
				$player->teleport(new Position($data['X'],$data['Y'],$data['Z'],$this->getServer()->getLevelByName($data['Level'])));
				$player->sendMessage('§4> §7传送成功! 已将你传送到 '.$args[0]);
				$player->sendMessage('§4> §7您当前的世界: '.$data['Level']);
				$this->TpaSoundParticle($player,1);
				break;
			case '地标列表':
			case 'warplist':
			    if(!isset($args[0]) or !is_numeric($args[0]) or $args[0]<1 or floor($args[0])!=$args[0]){
					 $player->sendMessage('§4> §7页码错误 页码应该为 大于0 的整数');
			         $player->sendMessage('§4>> §2/warplist|地标列表 页码   §7查看所有地标');
					 break;
				}
			
			    $warps = $this->warp->getAll();
				//§0--------[ §2浮空字帮助 §0]--------
				$player->sendMessage('§6-------< 地标列表 第§b'.$args[0].'§6页 -------< ');
			    $send = null;
				$c = 0;
				$page = 1;
				foreach(array_keys($warps) as $warpname){
					    $c++;
						if($c==7)
							 $page++;
						if($page==$args[0]){
						     $data = $this->warp->get($warpname);
				             $send .= "§3名称: §7{$warpname} §3所在世界: §7{$data['Level']} \n";
						}
				}
				if($send===null)
					$send = "§3页码 {$args[0]} 管理员尚未保存任何坐标";
				$player->sendMessage($send);
				$this->TpaSoundParticle($player,2);
			    break;
			case '全体传送':
			case 'tpall':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $cc = $this->con->get('全体传送');
				if($cc['全体可用']==false){
					if($cc['OP  可用']==true){
						if(!$player->isOp()){
							$player->sendMessage('§4> §7当前模式下只有 §4op §7有权限使用该指令.');
						    break;
						}
					}
					
				} 
				
				$Money  = EconomyAPI::getInstance()->myMoney($name);				
                $TpCost = $cc['全体传送价格'];
				if($Money < $TpCost  ){
					$player->sendMessage('§4> §7您的余额不足无法进行全体传送');
					$player->sendMessage('§4> §7全体传送需要 §2'.$TpCost.' §7银币而你只有 §2'.$Money.' §7银币');
					break;}
				
				foreach($this->getServer()->getOnlinePlayers() as $p){
				    $n = strtolower($p->getName());
		    		$data = $this->player->get(strtolower($n)); 
	    			if(  $data['RequestTime'] < time()  ){
					    	$data = $this->player->get($n);
							$data['Request']=$name;
							$data['RequestTime']=time()+30;
							$data['RequestType']=2;
							$this->player->set($n,$data);
							$this->player->save();
							//§0--------[ §2浮空字帮助 §0]--------
					        $p->sendMessage('§6-------< 全体传送 >-------');
							$p->sendMessage('§4> §3'.$NAME.' §7同意该请求将使你传送到他的位置');
							$p->sendMessage('§4> §7输入 §2/同意 §7来同意他的请求');
							$p->sendMessage('§4> §7输入 §2/拒绝 §7来拒绝他的请求');
							$p->sendMessage('§4> §7你有 §245 §7秒的时间考虑');
		    			}
                }
				EconomyAPI::getInstance()->reduceMoney($name,$TpCost);	
                unset($data,$Money,$TpCost,$cc);
				$this->TpaSoundParticle($player,2);				
			    break;
			case '传送帮助':
			case 'tpahelp':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    if(!isset($args[0]) or $args[0]==1){
							//§0--------[ §2浮空字帮助 §0]--------
				    $player->sendMessage('§6-------< 传送帮助 §b1/2 §6>-------');
			        $player->sendMessage('§f- §a/传送|tpa 玩家名     §7//请求传送到该玩家的位置');
			        $player->sendMessage('§f- §a/返回|back             §7//回到上次死亡点');
			        $player->sendMessage('§f- §a/同意|拒绝             §7//同意拒绝当前请求');
			        $player->sendMessage('§f- §a/传送帮助|tpahelp 页码   §7//查看传送帮助');
			        $player->sendMessage('§f- §a/传送到这|tpahere 玩家名   §7//请求该玩家传送到你的位置');	
			        //$player->sendMessage('§f- §a/随机传送|wild       §7//随机流放到当前世界的一个地方');
				}else if($args[0]==2){
				      $player->sendMessage('§6-------< 传送帮助 §b2/2 §6>-------');
			         $player->sendMessage('§f- §a/warp|地标 地标名|地标ID   §7//传送到一个地标位置');
			         $player->sendMessage('§f- §a/setwarp|设置地标 地标名   §7//以当前位置设置一个地标');
			         $player->sendMessage('§f- §a/warplist|地标列表 页码   §7//查看所有地标');
			         $player->sendMessage('§f- §a/安家|sethome        §7//设置家的位置');
			         $player->sendMessage('§f- §a/回家|home           §7//回到设置的家的位置');					
				     if([$this->con->get('全体传送')]['全体可用']==true){
					      $player->sendMessage('§f- §a/全体传送|tpall      §7//同意拒绝当前请求');
			 	     }
				}	
				$this->TpaSoundParticle($player,2);
			    break;
			case '随机传送':
			case 'wild':
		  	    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $player->sendMessage('§4> §7由于该功能出现了一个重大的bug所以被作者关闭了');
				break;
				$player->teleport(new Position(mt_rand(5,200),mt_rand(30,70),mt_rand(5,200),$player->getLevel()));
				$this->TpaSoundParticle($player);
			    break;
			case 'tpahere':
			case '传送到这':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    if(!isset($args[0]) || $args[0]==null){
					$player->sendMessage('§4> §7输入错误 格式§2/传送到这|tpahere 玩家名 §7请求该玩家传送到你的位置');
					break;}
					
				$data = $this->player->get(strtolower($args[0])); 
                if(!$this->isOnline($args[0]) || $data==null){
					$player->sendMessage('§4> §7该玩家不存在或不在线, 请检查名称拼写');
					break;}
				
				if(  $data['RequestTime'] >= time()  ){
					$player->sendMessage('§4> §7已经有人提早邀请他了');
					$player->sendMessage('§4> §7请等待他的选择或者等待一段时间当前邀请过期后再发出请求');
					break;}
				
                $Money  = EconomyAPI::getInstance()->myMoney($name);				
                $TpCost = $this->con->get('传送价格');
				if(  $this->con->get('传送付费')==true && $Money < $TpCost  ){
					$player->sendMessage('§4> §7您的余额不足无法进行传送');
					$player->sendMessage('§4> §7传送需要 §2'.$TpCost.' §7银币而你只有 §2'.$Money.' §7银币');
					break;}

                if($this->con->get('传送付费')==true){
					EconomyAPI::getInstance()->reduceMoney($name,$TpCost);
					foreach($this->getServer()->getOnlinePlayers() as $P){
						$n = strtolower($P->getName());
						if($n==$args[0]){
							$P->sendMessage('§6-------< 单人传送 >-------');
							$P->sendMessage('§4> §3'.$NAME.' §7请求你传送至他的位置');
							$P->sendMessage('§4> §7输入 §2/同意 §7来同意他的请求');
							$P->sendMessage('§4> §7输入 §2/拒绝 §7来拒绝他的请求');
							$P->sendMessage('§4> §7你有 §230 §7秒的时间考虑');
							$data = $this->player->get($n);
							$data['Request']=$name;
							$data['RequestTime']=time()+30;
							$data['RequestType']=2;
							$this->player->set($n,$data);
							$this->player->save();
							$player->sendMessage('§4> §7扣除 §2'.$TpCost.' §7银币, 已成功发送请求, 请等待结果.');
						}
					}
				}else{
					foreach($this->getServer()->getOnlinePlayers() as $P){
						$n = strtolower($P->getName());
						if($n==$args[0]){
							$P->sendMessage('§6-------< 单人传送 >-------');
							$P->sendMessage('§4> §3'.$NAME.' §6请求你传送至他的位置');
							$P->sendMessage('§4> §7输入 §2/同意 §7来同意他的请求');
							$P->sendMessage('§4> §7输入 §2/拒绝 §7来拒绝他的请求');
							$P->sendMessage('§4> §7你有 §230 §7秒的时间考虑');
							$data = $this->player->get($n);
							$data['Request']=$name;
							$data['RequestTime']=time()+30;
							$data['RequestType']=2;
							$this->player->set($n,$data);
							$this->player->save();
							$player->sendMessage('§4> §7已成功发送请求, 请等待结果.');
						}
					}
				}
				$this->TpaSoundParticle($player,2);
                unset($data,$Money,$TpCost);				
			    break;
				$this->TpaSoundParticle($player,1);
			case 'tpa':
			case '传送':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    if(!isset($args[0]) || $args[0]==null){
					$player->sendMessage('§4> §7输入错误 格式§2/传送|tpa 玩家名 §7请求到该玩家的位置');
					break;}
					
				$data = $this->player->get(strtolower($args[0])); 
                if(!$this->isOnline($args[0]) || $data==null){
					$player->sendMessage('§4> §7该玩家不存在或不在线, 请检查名称拼写');
					break;}
              
				if(  $data['RequestTime'] >= time()  ){
					$player->sendMessage('§4> §7已经有人提早邀请他了');
					$player->sendMessage('§4> §7请等待他的选择或者等待一段时间当前邀请过期后再发出请求');
					break;}
				
                $Money  = EconomyAPI::getInstance()->myMoney($name);				
                $TpCost = $this->con->get('传送价格');
				if(  $this->con->get('传送付费')==true && $Money < $TpCost  ){
					$player->sendMessage('§4> §7您的余额不足无法进行传送');
					$player->sendMessage('§4> §7传送需要 §2'.$TpCost.' §7银币而你只有 §2'.$Money.' §7银币');
					break;}
					
				if($this->con->get('传送付费')==true){
					EconomyAPI::getInstance()->reduceMoney($name,$TpCost);
					foreach($this->getServer()->getOnlinePlayers() as $P){
						$n = strtolower($P->getName());
						if($n==$args[0]){
							$P->sendMessage('§6-------< 单人传送 >-------');
							$P->sendMessage('§4> §3'.$NAME.' §7请求传送到你这');
							$P->sendMessage('§4> §7输入 §2/同意 §7来同意他的请求');
							$P->sendMessage('§4> §7输入 §2/拒绝 §7来拒绝他的请求');
							$P->sendMessage('§4> §7你有 §230 §7秒的时间考虑');
							$data = $this->player->get($n);
							$data['Request']=$name;
							$data['RequestTime']=time()+30;
							$data['RequestType']=1;
							$this->player->set($n,$data);
							$this->player->save();
							$player->sendMessage('§4> §7扣除 §2'.$TpCost.' §7银币, 已成功发送请求, 请等待结果.');
						}
					}
				}else{
					foreach($this->getServer()->getOnlinePlayers() as $P){
						$n = strtolower($P->getName());
						if($n==$args[0]){
							$P->sendMessage('§6-------< 单人传送 >-------');
							$P->sendMessage('§4> §3'.$NAME.' §7请求传送到你这');
							$P->sendMessage('§4> §7输入 §2/同意 §7来同意他的请求');
							$P->sendMessage('§4> §7输入 §2/拒绝 §7来拒绝他的请求');
							$P->sendMessage('§4> §7你有 §230 §7秒的时间考虑');
							$data = $this->player->get($n);
							$data['Request']=$name;
							$data['RequestTime']=time()+30;
							$data['RequestType']=1;
							$this->player->set($n,$data);
							$this->player->save();
							$player->sendMessage('§4> §7已成功发送请求, 请等待结果.');
						}
					}
				}
				$this->TpaSoundParticle($player,2);
                unset($data,$Money,$TpCost);				
			    break;
			case '安家':
			case 'sethome':
		    	//IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $data = $this->player->get($name);
				if($data['Home']!=null){
					$player->sendMessage('§4> §7正在覆盖已存在的家');
			    }
				
				$Money  = EconomyAPI::getInstance()->myMoney($name);
                $Cost   = $this->con->get('安家价格');				
				if($this->con->get('安家付费')==true && $Money < $Cost){
					$player->sendMessage('§4> §7您的余额不足设置家的位置');
					$player->sendMessage('§4> §7传送需要 §2'.$Cost.' §7银币而你只有 §2'.$Money.' §7银币');
					break;}
				//扣钱机2016.4.17
			    EconomyAPI::getInstance()->reduceMoney($name,$Cost);	
				
				$data = $this->player->get($name);
			    $data['Home']['X']=$player->getX();
			    $data['Home']['Y']=$player->getY();
			    $data['Home']['Z']=$player->getZ();
			    $data['Home']['L']=$player->getLevel()->getName();
				$this->player->set($name,$data);
				$this->player->save();
				$player->sendMessage('§4> §7已保存家的位置, 输入§2/回家 §7即可回到温暖的家了');
				$this->TpaSoundParticle($player,2);
				break;
			case '回家':
			case 'home':
		    	//IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $data = $this->player->get($name);
				if($data['Home']==null){
					$player->sendMessage('§4> §7你还没有记录家的位置呢, 输入§2/安家 §7记录吧');
			    break;}
				
				//扣钱机制累了2016年2月16日20:10:06有空写
				$player->sendMessage('§4> §7正在传送...');
				$data = $data['Home'];
				$player->teleport(   new Position(  $data['X'],$data['Y'],$data['Z'],$this->getServer()->getLevelByName($data['L'])  )   );
				$this->TpaSoundParticle($player,1);
				$player->sendMessage('§4> §7传送成功');
			    break;
			case '返回':
			case 'back':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $data = $this->player->get($name);
				if($data['Back']==null){
					$player->sendMessage('§4> §7没有死亡记录 §2/返回|back');
				    break;}
				$data = $data['Back'];
				$player->teleport(     new Position(  $data['X'],$data['Y'],$data['Z'],$this->getServer()->getLevelByName($data['L'])  )        );
            	$data = $this->player->get($name);
            	$data['Back']=null;
	    		$this->player->set($name,$data);
	    		$this->player->save();
				$player->sendMessage('§4> §7传送成功, 已将您传送到上次死亡地.');
				$this->TpaSoundParticle($player,1);
				
			    break;
			case '同意':
			case 'yes':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
			    $data = $this->player->get($name);
				if($data['RequestTime']==null){
					$player->sendMessage('§4> §7还木有任何人对你发出传送请求呢');
					break;}
				
				if(  $data['RequestTime'] < time()  ){
					$player->sendMessage('§4> §7当前请求已失效, 请让对方重新发送请求.');
					break;}
				
				if(!$this->isOnline($data['Request'])){
					$player->sendMessage('§4> §7该玩家已离线.');
					break;}
					
				$data = $this->player->get($name);	
				if($data['RequestType']==1){
					foreach($this->getServer()->getOnlinePlayers() as $P){
						if(strtolower($P->getName())==$data['Request']){
							//$P是请求传送到$player这的人
							$P->teleport(new Position($player->getX(),$player->getY(),$player->getZ(),$player->getLevel()));
							$this->TpaSoundParticle($player);
						    $P->sendMessage('§4> §7传送成功.');
						}
					}
				}else if($data['RequestType']==2){
					foreach($this->getServer()->getOnlinePlayers() as $P){
						if(strtolower($P->getName())==$data['Request']){
							//$player是请求传送到$P这的人
							$player->teleport(new Position($P->getX(),$P->getY(),$P->getZ(),$P->getLevel()));
							$this->TpaSoundParticle($player);
							$P->sendMessage('§4> §7传送成功.');
						}
					}
				}
				$data['Request']=null;
				$data['RequestTime']=null;
				$data['RequestType']=null;
				$this->player->set($name,$data);	
			    $this->player->save();
				$this->TpaSoundParticle($player,1);
				$player->sendMessage('§4> §7传送成功.');
				break;
			case '拒绝':
			case 'no':
			    //IF(!$player instanceof Player){$player->sendMessage('§4> §7请不要在控制台输入本指令');break;}
				$data = $this->player->get($name);
				if($data['RequestTime']==null){
					$player->sendMessage('§4> §7还木有任何人对你发出传送请求呢');
					break;}
				
				if(  $data['RequestTime'] < time()  ){
					$player->sendMessage('§4> §7当前请求已失效, 请让对方重新发送请求.');
					break;}
					
				$data['Request']=null;
				$data['RequestTime']=null;
				$data['RequestType']=null;
				$this->player->set($name,$data);	
			    $this->player->save();	
				$player->sendMessage('§4> §7已拒绝对方的传送请求');
				$this->TpaSoundParticle($player,3);
			    break;
		}
		}
	}
	
}