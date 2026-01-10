<?php

namespace IFloatingText;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use pocketmine\utils\Utils;

use onebone\economyapi\EconomyAPI;      
  
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender; 

use pocketmine\math\Vector3;
use pocketmine\level\Position;        
use pocketmine\level\Level;

use pocketmine\block\Block;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\event\entity\EntityTeleportEvent;

use pocketmine\level\particle\FloatingTextParticle;
########################################
#   浮空字
#   本插件开源禁止倒卖人人有责
#   作者 ibook 
#   @qq: 1542660471
#   bug反馈群(qq): 546488737
#   为了照顾新人已在难点加注释
########################################
class IFloatingText extends PluginBase implements Listener{

    public function onEnable()
    {
    $path = $this->getDataFolder();
    
		$this->getLogger()->info(TextFormat::GREEN.'数据读取中..');
		if(!is_dir($path)){
			$this->getLogger()->info(TextFormat::GREEN.'正在为第一次使用本插件创建数据库...');
			@mkdir($path);
  		$this->saveResource('IConfig.yml',true);
  		$this->saveResource('IDate.yml',true);
  		$this->con = new Config($path.'IConfig.yml', Config::YAML);
	   	$this->date = new Config($path.'IDate.yml', Config::YAML);
			$this->getLogger()->info(TextFormat::GREEN.'数据库创建成功！');
	  }else{
	  	$this->con = new Config($path.'IConfig.yml', Config::YAML);
	  	if($this->con->get('版本')==null){
				$this->getLogger()->info(TextFormat::GREEN.'正在更新数据库...');
  			$this->saveResource('IDate.yml',true);
	    	$this->date = new Config($path.'IDate.yml', Config::YAML);
	  		$this->date->setAll($this->con->getAll());
	  		$this->date->save();
	  		$this->saveResource('IConfig.yml',true);
	  		$this->con = new Config($path.'IConfig.yml', Config::YAML);
				$this->getLogger()->info(TextFormat::GREEN.'更新数据库更新完成！');
	  	}else{
	  		$this->con = new Config($path.'IConfig.yml', Config::YAML);
	    	$this->date = new Config($path.'IDate.yml', Config::YAML);
	  	}
	  }
		$this->getLogger()->info(TextFormat::GREEN.'数据读取成功！');
	  
	  
  	   $this->saveResource('Uplog.yml',true);
		
	  $this->type = $this->con->get('执行模式');  
	  //$this->type = 1;  
		//$this->getLogger()->info(TextFormat::GREEN .'正在解析数据中....');
		//$date = $this->date->getAll();
		//if($date==null)
			//$this->getLogger()->info(TextFormat::GREEN .'没有数据,正在关闭解析');
		//else{
			//$c=0;
			//foreach($date as $index=>$value){
				//$index = explode(':',$index);
				//$this->getServer()->getLevelByName($index[3])->addParticle(new FloatingTextParticle(new Vector3($index[0],$index[1],$index[2]),$value['message'],$value['head']));
			  //  $c++;
			//}
			//$this->getLogger()->info(TextFormat::GREEN . "完成解析 $c 个浮空字");
		//}
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::GREEN . 'IFloatingText v1.1.0 For API3.0.0+ 已安全启动');
		$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
		$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
	}

	public function reloadFloatText($is_set_other_null=false,string $set_level){
	  $c=0;
		foreach($this->date->getAll() as $index=>$value){
			$index = explode(':',$index);//X:Y:Z:L
			if($index[3]==$set_level){
				$this->getServer()->getLevelByName($index[3])->addParticle(new FloatingTextParticle(new Vector3($index[0],$index[1],$index[2]),$value['head'],$this->turn($value['message'])));
				$c++;
			}else if($is_set_other_null==true){
				$this->getServer()->getLevelByName($index[3])->addParticle(new FloatingTextParticle(new Vector3($index[0],$index[1],$index[2]),null,null));
				$c++;
			}
		}
		return $c;
	}
	
	public function turn(string $source){
		if(explode('\n',$source)===$source){
			return $source;
		}else{
			$turned = null;
			foreach(explode('\n',$source) as $half){
				$turned .= $half."\n";
			}
		}
		return $turned;
	}

	public function onCommand(CommandSender $player, Command $cmd, $label, array $args)
	{  
	    
	    $name = strtolower($player->getName());
	  if($cmd=='ft'){
		if(!$player->isOp())
			return;
		if(!isset($args[0])){
			$player->sendMessage('§4>> §7输入有误请输入 §a/ft help §7查看帮助');
			return;
		}
    	switch($args[0]){
			case 'help':
			    $player->sendMessage('§0--------[ §2浮空字帮助 §0]--------');
			    $player->sendMessage('§f- §a/ft help                    §7//查看帮助');
			    $player->sendMessage('§f- §a/ft set 标题 文字       §7//在当前位置设置一个浮空字');
			    $player->sendMessage('§f- §a/ft remove 坐标         §7//移除此坐标的浮空字(坐标以X:Y:Z:Level的方式排列)');
			    break;
			case 'set':
				if(!isset($args[2])){
		    		$player->sendMessage('§4>> §7输入有误请输入 §a/ft help §7查看帮助');
				    break;}
					
				$x = floor($player->getX());
				$y = floor($player->getY());
				$z = floor($player->getZ());
				$l = $player->getLevel();
				$l_n = $l->getName();
				$this->date->set("$x:$y:$z:$l_n",array('head'=>$args[1],'message'=>$args[2]));
				$this->date->save();
				$pos = new Vector3($x,$y,$z);
				$l->addParticle(new FloatingTextParticle($pos,$args[2],$this->turn($args[1])));
				$player->sendMessage('§4>> §7设置成功, 看看你的脚下');
				
			    break;
			case 'remove':
			    if(!isset($args[1])){
		    		$player->sendMessage('§4>> §7输入有误请输入 §a/ft help §7查看帮助');
				    break;}
					
			    if($this->date->get($args[1])==null){
		    		$player->sendMessage('§4>> §7没有这个坐标的浮空字查看浮空字坐标请查看PM目录下的'.$this->getDataFolder().'IConfig.yml文件');
				    break;}
					
				$this->date->remove($args[1]);
				$this->date->save();
				$player->sendMessage('§4>> §7成功移除');
				$index=explode(":",$args[1]);
				$this->getServer()->getLevelByName($index[3])->addParticle(new FloatingTextParticle(new Vector3($index[0],$index[1],$index[2]),null,null));
			    break;
			default:
				$player->sendMessage('§4>> §7输入有误请输入 §a/ft help §7查看帮助');
			    break;
		}
	  }
	}
	
	public function onRespawn(PlayerRespawnEvent $event){
		switch($this->type){
			case 0:
				$this->reloadFloatText(true,$event->getPlayer()->getLevel()->getName());
				break;
			case 1:
				$this->reloadFloatText(false,$event->getPlayer()->getLevel()->getName());
				break;
		}
	}
	
	public function onTeleport(EntityTeleportEvent $event){
		if($event->getEntity() instanceof Player){    	
			switch($this->type){
				case 0:
					$this->reloadFloatText(true,$event->getTo()->getLevel()->getName());
					//$this->getLogger()->info('传送修复 01');
					break;
				case 1:
					$this->reloadFloatText(false,$event->getTo()->getLevel()->getName());
					//$this->getLogger()->info('传送修复 02'.$event->getTo()->getLevel()->getName());
					break;
			}
		}
	}
	
}