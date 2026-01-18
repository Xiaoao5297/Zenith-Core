<?php

namespace ICurrency;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
/*
*  对开发者的忠告: 本经济插件完全开源
*  请在传值$name无需将$name小写 已在函数内处理 
*  可作为第二货币(点劵) 欢迎大家一起参与开发改进
*  ICurrency IC 货币
*  调用方式 use ICurrency\ICurrency;
*  ICurrency::函数名
*/
class ICurrency extends PluginBase implements Listener{
    
	/*
	*  object $config,$playerDate instance of Config;
	*/
	public $config,$player;
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		@mkdir($this->getDataFolder());
		$this->player = new Config($this->getDataFolder() . "player.yml", Config::YAML,array());
		$this->config = (new Config($this->getDataFolder() . "config.yml", Config::YAML,
		array(
		    '初始货币数量'=>0,
		    '货币名称'=>'金币',
            '货币交易'=>true,			
		)))->getAll();
			$this->getLogger()->info(TextFormat::GREEN . 'ICurrency v1.0.2 For API3.0.0+ 已安全启动');
			$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
			$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
		$this->saveResource('Uplog.txt',true);
	}

	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if($cmd=='ic'){
			$name = strtolower($sender->getName());
		  if(!isset($args[0])){
			  $sender->sendMessage('>> 输入/ic help 查看帮助');
			  //break;
		  }else{
			switch($args[0]){
				case 'help':
				    $sender->sendMessage('-------< IC货币帮助 >-------');
				    if($this->config['货币交易']){
					 $sender->sendMessage('- /ic pay 数量 玩家 //支付玩家一定的货币');
			        }else{
					 $sender->sendMessage('- /ic pay 数量 玩家     //支付玩家一定的货币 [由于货币交易被关闭这项指令无法使用]');
					}
					if($sender->isOp()){
				     $sender->sendMessage('- /ic set 数量 玩家     //设置玩家的货币数量');
				     $sender->sendMessage('- /ic reset 玩家        //将玩家的货币数量设置为初始值');
				     $sender->sendMessage('- /ic add 数量 玩家     //添加玩家的货币数量');
				     $sender->sendMessage('- /ic reduce 数量 玩家  //减少玩家的货币数量');
					}else{
				     $sender->sendMessage('- /ic reset             //将我的货币数量设置为初始值');
					}
					break;
				case 'pay':
				    $args[2] = strtolower($args[2]);
				    if($this->config['货币交易']){
					 if(!isset($args[1])||!is_numeric($args[1]) || $args[1]<=0){
					  $sender->sendMessage('>> 交易的'.$this->config['货币名称'].'数量必需为整数且, 大于0');
					 }else if($this->getCoin($name)<$args[1]){
					  $sender->sendMessage('>> 你没有足够的'.$this->config['货币名称'].'来支付本次交易');
					 }else if(!isset($args[2]) || $this->getCoin($args[2])==null){
					  $sender->sendMessage('>> 玩家不存在或未输入玩家名! ');
					 }else{
					  $this->reduceCoin($name,$args[1]);
					  $this->addCoin($args[2],$args[1]);
					  $sender->sendMessage('>> 支付成功已扣除您$'.$args[1].'枚'.$this->config['货币名称'].'! ');
					  foreach($this->getServer()->getOnlinePlayers() as $p){
					   if(strtolower($p->getName())==strtolower($args[2])){
						$p->sendMessage('>> 您获得了来自'.$name.'的'.$args[1].'枚'.$this->config['货币名称'].'! ');
					   }
					  }
					 }
					}else{
					 $sender->sendMessage('>> 由于'.$this->config['货币名称'].'交易被关闭这项指令无法使用');
					}
				    break;
				case 'set':
				    $args[2] = strtolower($args[2]);
				    if($sender->isOp()){
					 if(!isset($args[1])||!is_numeric($args[1]) || $args[1]<=0){
					  $sender->sendMessage('>> 设置的'.$this->config['货币名称'].'数量必需为整数且, 大于0');
					 }else{
					  if(!isset($args[2])){
					   $this->setCoin($name,$args[1]);
					   $sender->sendMessage('>> 已设置你的'.$this->config['货币名称'].'数量');
					  }else{
					   if($this->getCoin($args[2])==null){
						$sender->sendMessage('>> 该玩家不存在! ');
					   }else{
						$this->setCoin($args[2],$args[1]);
						$sender->sendMessage('>> 已设置对方的'.$this->config['货币名称'].'数量');
						foreach($this->getServer()->getOnlinePlayers() as $p){
					     if(strtolower($p->getName())==strtolower($args[2])){
						 $p->sendMessage('>> 您的'.$this->config['货币名称'].'被'.$name.'添加了'.$args[1].'枚! ');
					     }
					    }
					   }
					  }
					 }
					}else{
					 $sender->sendMessage('>> 权限不足这个指令为管理员使用');
					}
				    break;
				case 'reset':
				    $args[1] = strtolower($args[1]);
				    if($sender->isOp()){
					 if(!isset($args[1])){
					  $this->resetCoin($name);
					  $sender->sendMessage('>> 已重设你的'.$this->config['货币名称'].'数量');
					 }else if($this->getCoin($args[1])==null){
					  $sender->sendMessage('>> 玩家不存在! ');
					 }else{
					  $this->resetCoin($args[1]);
					  $sender->sendMessage('>> 已重设对方的'.$this->config['货币名称'].'数量');
					  foreach($this->getServer()->getOnlinePlayers() as $p){
					   if(strtolower($p->getName())==strtolower($args[2])){
					    $p->sendMessage('>> 您的'.$this->config['货币名称'].'被'.$name.'设置为默认初始数量'.$this->config['初始货币数量'].'枚! ');
					   }
					  }
					 }
					}else{
					 $sender->sendMessage('>> 权限不足这个指令为管理员使用');
					}
				    break;
				case 'add':
				    if($sender->isOp()){
				     if(!isset($args[1])||!is_numeric($args[1]) || $args[1]<=0){
					  $sender->sendMessage('>> 添加的'.$this->config['货币名称'].'数量必需为整数且, 大于0');
					 }else{
					  if(!isset($args[2])){
					   $this->addCoin($name,$args[1]);
					   $sender->sendMessage('>> 已添加你的'.$this->config['货币名称'].'数量');
					  }else{
					   if($this->getCoin($args[2])==null){
						$sender->sendMessage('>> 该玩家不存在! ');
					   }else{
						$this->addCoin($args[2],$args[1]);
						$sender->sendMessage('>> 已添加对方的'.$this->config['货币名称'].'数量');
						foreach($this->getServer()->getOnlinePlayers() as $p){
					     if(strtolower($p->getName())==strtolower($args[2])){
						 $p->sendMessage('>> 您的'.$this->config['货币名称'].'被'.$name.'设置为'.$args[1].'枚! ');
					     }
					    }
					   }
					  }
					 }
					}else{
					 $sender->sendMessage('>> 权限不足这个指令为管理员使用');
					}
				    break;
				case 'reduce':
				    if($sender->isOp()){
				     if(!isset($args[1])||!is_numeric($args[1]) || $args[1]<=0){
					  $sender->sendMessage('>> 减少的'.$this->config['货币名称'].'数量必需为整数且, 大于0');
					 }else{
					  if(!isset($args[2])){
					   $this->reduceCoin($name,$args[1]);
					   $sender->sendMessage('>> 已减少你的'.$this->config['货币名称'].'数量');
					  }else{
					   if($this->getCoin($args[2])==null){
						$sender->sendMessage('>> 该玩家不存在! ');
					   }else{
						$this->reduceCoin($args[2],$args[1]);
						$sender->sendMessage('>> 已减少对方的'.$this->config['货币名称'].'数量');
						foreach($this->getServer()->getOnlinePlayers() as $p){
					     if(strtolower($p->getName())==strtolower($args[2])){
						 $p->sendMessage('>> 您的'.$this->config['货币名称'].'被'.$name.'减少了'.$args[1].'枚! ');
					     }
					    }
					   }
					  }
					 }
					}else{
					 $sender->sendMessage('>> 权限不足这个指令为管理员使用');
					}
				    break;
				default:
				    $sender->sendMessage('>> 输入/ic help 查看帮助');
				    break;
			}
		  }
		}
	}
	
	//在玩家加入时创建初始数据
    public function onJoin(playerJoinEvent $event){
		$name=strtolower($event->getPlayer()->getName());
		if($this->getCoin($name)==null)
	    	$this->resetCoin($name);
	}
	
	//设置玩家的货币数量 (string $name,int $number)
	final public function setCoin($name,$number){
		$name=strtolower($name);
		$this->player->set($name,$number);
		$this->player->save();
	}
	
	//得到玩家的货币数量 (string $name)
	final public function getCoin($name){
		$name = strtolower($name);
		return $this->player->get($name);
	}
	
	//减少玩家的货币数量 (string $name,int $number)
	final public function reduceCoin($name,$number){
		$this->setCoin($name,$this->getCoin($name) - $number);
	}
	
	//添加玩家的货币数量 (string $name,int $number)
	final public function addCoin($name,$number){
		$this->setCoin($name,$this->getCoin($name) + $number);
	}
	
	//重设玩家的货币数量 (string $name)
	final public function resetCoin($name){
		$this->setCoin($name,$this->config['初始货币数量']);
	}
}