<?php

namespace ITouchShop;

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

use pocketmine\item\Item;

use pocketmine\block\Air;
use pocketmine\block\Block;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\inventory\InventoryCloseEvent;

use pocketmine\inventory\ChestInventory;

use pocketmine\level\particle\FloatingTextParticle;

class ITouchShop extends PluginBase implements Listener{
	
/*
*  $Pos[$name][x]=; string
*  $Pos[$name][y]=; string
*  $Pos[$name][z]=; string
*  $Pos[$name][level]=; object
*  $Pos[$name][id]=; int   
*/
protected $Pos;

    public function onEnable()
    {
		if(!is_dir($this->getDataFolder())){
		    $this->getLogger()->info(TextFormat::GREEN.'正在为第一次运行本程序做准备...');
			@mkdir($this->getDataFolder());      
    	    $this->config = (new Config($this->getDataFolder().'IConfig.yml', Config::YAML))->getAll();
			$this->getLogger()->info(TextFormat::GREEN . 'ITouchShop v1.0.1 For API3.0.0+ 已安全启动');
			$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
			$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
	    }else{
			$this->config = (new Config($this->getDataFolder().'IConfig.yml', Config::YAML))->getAll();
		}
		$this->shop = new Config($this->getDataFolder().'IShop.yml', Config::YAML);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info(TextFormat::GREEN . 'ITouchShop v1.0.1 For API3.0.0+ 已安全启动');
		$this->getLogger()->info(TextFormat::GREEN . 'All by ibook');
		$this->getLogger()->info(TextFormat::GREEN . '欢迎加群为我们提供创意和线索! Q群 546488737 ');
	
		$this->saveResource('Uplog.txt',true);
	}

	public function isIdType($id){
		if(!is_numeric($id))
			$id = explode(':',$id);
		else
			return true;
		
		switch(count($id)){
			case 1:
			    if(!is_numeric($id[0]))
					return false;
				break;
			case 2:
			    if(!is_numeric($id[0]) or !is_numeric($id[1]))
			        return false;
				break;
			default:
			    return false;
		}
		
	    return true;
	} 
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{  
	    $name = strtolower($sender->getName());
	  if($cmd=='ts'){
		if(!$sender->isOp()){
 	        $sender->sendMessage('§4输入有误请输入 §a/help §4查看帮助');	
     		return;
		}
    	switch($args[0]){
			case 'help':
			    $sender->sendMessage('§0--------[ §2触摸商店帮助 §0]--------');
			    $sender->sendMessage('§f- §a/ts help                §7//查看帮助');
			    $sender->sendMessage('§f- §a/ts set 价格 数量 [id]  §7//设置一个商店ID不填为点击方块id');
			    $sender->sendMessage('§f- §a/ts remove              §7//添加一个物品');
			    break;
			case 'set':
				if(!isset($this->Pos[$name])){
					$sender->sendMessage('§4>> §7未记录方块位置,请点击一个方块');
				    break;}
				
				if(!isset($args[2]) and !is_numeric($args[1]) and !is_numeric($args[2])){
					$sender->sendMessage('§4>> §7输入有误请输入 §a/ts help §7查看帮助');
				    break;}
					
				if(isset($args[3])){
					if(!$this->isIdType($args[3])){
						$sender->sendMessage('§4>> §7输入有误请输入 §a/ts help §7查看帮助');
						break;
					}else{
						$d['id']=$args[3];
					}
				}
				
				$d = $this->Pos[$name];
				$write = array(
				'number'=>$args[2],
				'price' =>$args[1],
				'id'=>$d['id'],
				);
				$this->shop->set($d['string'],$write);
				$this->shop->save();
				$sender->sendMessage('§4>> §7商店已设置');
				
			    break;
			case 'remove':
			    if(!isset($this->Pos[$name])){
					$sender->sendMessage('§4>> §7未记录方块位置,请点击一个方块');
				    break;}
				
				unset($this->Pos[$name]);
				$sender->sendMessage('§4>> §7数据已移除');
			    break;
			default:
				$sender->sendMessage('§4>> §7输入有误请输入 §a/ts help §7查看帮助');
			    break;
		}
	  }
	}
		
	public function onInteract(playerInteractEvent $event)
    {
		$b  = $event->getBlock();
		$player = $event->getPlayer();
		$name = strtolower($player->getName());
		//$pos = array($b->getX(),$b->getY(),$b->getZ(),$b->getLevel(),$b->getLevel()->getName(),$b->getId().':'.$b->getDamage());
		$string="{$b->getX()}:{$b->getY()}:{$b->getZ()}:{$b->getLevel()->getName()}";
		if($player->isOp()){
			$this->Pos[$name]=array('string'=>$string,'id'=>$b->getId().':'.$b->getDamage());
			$player->sendTip('已设置方块数据');
		}else{
			$date = $this->shop->get($string);
			if(!$date==null){
				$m = $date['price'];
				if(EconomyAPI::getInstance()->myMoney($name) < $m){
 	        	    $player->sendMessage('§4>> §7你的银币不足以支付 需要 【'.$m.'】 银币');
    				return;}
				$id = explode(':',$date['id']);
				$nu = $date['number'];
				EconomyAPI::getInstance()->reduceMoney($name,$m);
 	            $player->sendMessage('§4>> §7扣除 【'.$m.'】 银币, 获得了 【'.$nu.'】 个 id: 【'.$date['id'].'】 ');
				$player->getInventory()->addItem(new Item($id[0],$id[1],$date['number']));
			}
		}
	}
	
	public function onBreak(BlockBreakEvent $event)
    {
		$b  = $event->getBlock();
		$player = $event->getPlayer();
		$name = strtolower($player->getName());
		//$pos = array($b->getX(),$b->getY(),$b->getZ(),$b->getLevel(),$b->getLevel()->getName(),$b->getId());
		$string="{$b->getX()}:{$b->getY()}:{$b->getZ()}:{$b->getLevel()->getName()}";
		if($this->shop->get($string)!=null){
		    if($player->isOp()){
			//if(){
			    $this->shop->remove($string);
		        $player->sendMessage('>> 商店移除成功');
			//}
		    }else{
				//$m = $date['price'];
				//if(EconomyAPI::getInstance()->myMoney($name) < $m){
 	            $player->sendMessage('§4>> §7此方块为系统商店方块, 只有OP可以移除');
 	            $event->setCancelled();
    			//}
		    }
		}
	}
}