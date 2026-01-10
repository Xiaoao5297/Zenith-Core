<?php
//作者Guestc  
   namespace FightTheSky;
   use spl\BaseClassLoader;
   use pocketmine\event\player\PlayerJoinEvent;
   use pocketmine\entity\Effect;
   use pocketmine\item\ItemBlock;
   use pocketmine\entity\InstantEffect; 
   use pocketmine\event\Listener;
   use pocketmine\Player;
   use pocketmine\plugin\PluginBase;
   use pocketmine\utils\TextFormat;
   use pocketmine\item\Item;
   use pocketmine\level\Level;
   use pocketmine\math\Vector3;
   use pocketmine\inventory\PlayerInventory; 
   use pocketmine\event\entity\EntityDamageByEntityEvent;
   use pocketmine\event\entity\EntityShootBowEvent;
   use pocketmine\block\block;
   
   use pocketmine\Server;
   use onebone\economyapi\EconomyAPI; 
   use pocketmine\event\entity\EntityDamageEvent;
   use pocketmine\inventory\Inventory;
   use pocketmine\entity\Entity;
   use pocketmine\scheduler\PluginTask;
   use pocketmine\utils\Config;
   use pocketmine\event\player\PlayerMoveEvent;
   use pocketmine\event\player\PlayerItemHeldEvent;
   use pocketmine\event\player\PlayerInteractEvent;
   use pocketmine\command\CommandSender;
   use pocketmine\command\Command;
   use pocketmine\event\entity\EntityDespawnEvent;

	use pocketmine\level\particle\BubbleParticle;
	use pocketmine\level\particle\CriticalParticle;
	use pocketmine\level\particle\DustParticle;
	use pocketmine\level\particle\EnchantParticle;
       use pocketmine\level\particle\EnchantmentTableParticle;
       use pocketmine\level\particle\HappyVillagerParticle;
       use pocketmine\level\particle\AngryVillagerParticle;
	use pocketmine\level\particle\ExplodeParticle;
	use pocketmine\level\particle\FlameParticle;
	use pocketmine\level\particle\HeartParticle;
       use pocketmine\level\particle\InstantEnchantParticle;
	use pocketmine\level\particle\InkParticle;
	use pocketmine\level\particle\ItemBreakParticle;
	use pocketmine\level\particle\LavaDripParticle;
	use pocketmine\level\particle\LavaParticle;
	use pocketmine\level\particle\Particle;
	use pocketmine\level\particle\MobSpawnParticle;
	use pocketmine\level\particle\PortalParticle;
	use pocketmine\level\particle\RedstoneParticle;
	use pocketmine\level\particle\SmokeParticle;
	use pocketmine\level\particle\SplashParticle;
	use pocketmine\level\particle\SporeParticle;
	use pocketmine\level\particle\TerrainParticle;
	use pocketmine\level\particle\WaterDripParticle;
	use pocketmine\level\particle\WaterParticle;
	use pocketmine\scheduler\CallbackTask;
   
   
 class Main extends PluginBase implements Listener{
	 public $fire_players = [];
	 public $gift_players = [];
	 public $ball_players = [];


   	public function onEnable(){
   		$this->getServer()->getPluginManager()->registerEvents($this,$this);
   		$this->getLogger()->info(TextFormat::GREEN." By: geustc   ");
		@mkdir($this->getDataFolder(),0777,true);
		@mkdir($this->getDataFolder() . "players/" ,0777,true);
		$this->firedata=new Config($this->getDataFolder() . "fire.yml",Config::YAML,array());
		$this->world=new Config($this->getDataFolder(). "canPvpWorld.yml",Config::YAML,array('world'=>array(),'particle'=>'on'));

   	}
   	 public function onDisable(){
   		$this->getLogger()->info(TextFormat::RED."Plugin Has Been Disable !");
   		}
		
//-------------------计算器
public function fire($name)
{
	$result=array_search($name,$this->fire_players);
	if($result !== false)
	{
		array_splice($this->fire_players,$result,1);
	}
	unset($result);
}
public function gift($name)
{
	$result=array_search($name,$this->gift_players);
	if($result !== false)
	{
		array_splice($this->gift_players,$result,1);
	}
	unset($result);
}
public function ball($name)
{
	$result=array_search($name,$this->ball_players);
	if($result !== false)
	{
		array_splice($this->ball_players,$result,1);
	}
	unset($result);
}
//-------------------指令
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args)
	{
		$pyml=new Config($this->getDataFolder() . "players/" . strtolower($pn=$sender->getName()) .".yml",Config::YAML,array());
		switch($cmd->getName())
		{
			case "fts":
			switch($args[0])
		{
			case "type":
			if($pyml->exists("type")){$sender->sendMessage("§4[FightTheSky] : 你的属性已设置-无法更改");}
			else{
			switch($args[1])
			{
				case "water"://水
				$pyml->set("type","water");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§1恭喜你历练成功，获得属性：怒涛之力！");
				break;
				case "fire"://火
				$pyml->set("type","fire");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§c恭喜你历练成功，获得属性：熔岩之力！");
				break;
				case "earth"://土
				$pyml->set("type","earth");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§e恭喜你历练成功，获得属性：磐石之力！");
				break;
				case "wood"://木
				$pyml->set("type","wood");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§5恭喜你历练成功，获得属性：暗影之力！");
				break;
				case "ice"://冰
				$pyml->set("type","ice");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§b恭喜你历练成功，获得属性：寒冰之力！");
				break;
				case "noxious"://毒
				$pyml->set("type","noxious");
				$pyml->save();
				$type=$pyml->get("type");
				$sender->sendMessage("§a[FightTheSky] : 你的属性已设置为 ： $type");
				break;
			}
			}
			break;
			case "worldadd":
              if(isset($args[1]) && $sender->isOp())
			  {
				  $level=$this->world->get('world');
				  $level[]=$args[1];
				  $this->world->set('world',$level);
				  $this->world->save();
				  $sender->sendMessage("§a[FightTheSky] : 已添加{$args[1]} 到PVP世界~");
			  }
			break;
			case "p":
			$particle=$this->world->get('particle');
			if($particle == 'on')
			{
				$this->world->set('particle','off');
				$sender->sendMessage("§7隐藏王者气息");
			}else{
				$this->world->set('particle','on');
				$sender->sendMessage("§a开启王者气息压制！");
			}
			break;
			
			case "setfire":
			if($sender->isOp() == true)
			{
			if(isset($args[1]))
			{
				if(isset($args[2]))
			{
				if($args[1]>10){$sender->sendMessage("§a此异火ID $args[1] 不存在");}else{
					$fd=$this->firedata;
					$fd->set($args[1],$args[2]);
					$fd->save();
					$sender->sendMessage("§a此异火ID $args[1] 的拥有者已设置为 ：$args[2]");
				}
			}
			}else{$sender->sendMessage("§a格式为 ：/fts fireset 异火ID 玩家ID");}
			}
			break;
			}
			break;
			
		}
		return true;
	}
	public function canPVP($level)
	{
		$w=$this->world->get('world');
		if(in_array($level->getName(),$w)){return true;}else{return false;}
	}
	
//------------------进入时
		public function onJoin(PlayerJoinEvent $event)
		{
			
		if(!(file_exists($this->getDataFolder() . "players/" . strtolower($pn=$event->getPlayer()->getName()).".yml")))
			{
			$pyml=new Config($this->getDataFolder() . "players/" . strtolower($pn) . ".yml",Config::YAML,array());
			$pyml->save();
			$event->getPlayer()->sendMessage("");
			return $pyml;
			}
			else{
				$pyml=new Config($this->getDataFolder() . "players/" . strtolower($pn) . ".yml",Config::YAML,array());
				return $pyml;
				}
		}
		
//------------------移动时
		public function onMove(PlayerMoveEvent $event)
		{
			$p=$event->getPlayer();
			$level=$p->getLevel();
			$pyml=new Config($this->getDataFolder() . "players/" . strtolower($pn=$event->getPlayer()->getName()) .".yml",Config::YAML,array());
			if($pyml->exists("type") && $this->world->get('particle') == 'on')
			{
			$x=$p->getX();
			$y=$p->getY();
			$z=$p->getZ();
			$rand = array(0.1,0.15,0.2,0.25,0.3);
			$b=array_rand($rand,1);
			{
				switch($pyml->get("type"))
				{
					case "water":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new TerrainParticle($v3,new Block(17)));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new WaterParticle($v3));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new WaterParticle($v3));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new WaterParticle($v3));
							}
							break;
						}
					}
					break;
					case "fire":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new LavaDripParticle($v3));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new LavaDripParticle($v3));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new LavaDripParticle($v3));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new LavaDripParticle($v3));
							}
							break;
						}
					}
					break;
					case "earth":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new InstantEnchantParticle($v3,50));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new InstantEnchantParticle($v3,50));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new instantEnchantParticle($v3,50));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new InstantEnchantParticle($v3,50));
							}
							break;
						}
					}
					break;
					case "wood":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new HappyVillagerParticle($v3));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new HappyVillagerParticle($v3));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new HappyVillagerParticle($v3));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new HappyVillagerParticle($v3));
							}
							break;
						}
					}
					break;
					case "ice":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new AngryVillagerParticle($v3));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new AngleVillagerParticle($v3));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new AngryVillagerParticle($v3));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new AngryVillagerParticle($v3));
							}
							break;
						}
					}
					break;
					case "noxious":
					for($a=0;$a<4;$a++)
					{
						
						switch($a)
						{
							case 0:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()+1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new EnchantParticle($v3));
							}
							break;
							case 1:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX()-1,$p->getY()+$b,$p->getZ());
									$level->addParticle(new EnchantParticle($v3));
							}
							break;
							case 2:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()+1);
									$level->addParticle(new EnchantParticle($v3));
							}
							break;
							case 3:
							for($one=0;$one<5;$one++)
							{	
									$v3=new Vector3($p->getX(),$p->getY()+$b,$p->getZ()-1);
									$level->addParticle(new EnchantParticle($v3));
							}
							break;
						}
					}
					break;
				}
			}
		}
	}

//-------------------攻击力
		public function onHurt(EntityDamageEvent $event){
			if($event->isCancelled() !== true){
      	if($event instanceof EntityDamageByEntityEvent && $event->isCancelled() !== false)
		{

      		if($damager instanceof Player)
			{
      			if($entity instanceof Player)
				{
      		$damager=$event->getDamager();
      		$entity=$event->getEntity();
			$pname=$damager->getName();
			$damage=$damager->getInventory()->getItemInHand()->getDamage();
			$mtrand=mt_rand(1,10);
      				if(!$event->isCancelled() && $this->canPVP($damager->getLevel()))
					{
			//数组
			$fire = array
			(
				array(351,0,"351:0","§c玩家 $pname 正在使用 [异火]-[NO.1]-[帝炎] 请勿靠近！"),
				array(351,1,"351:1","§c玩家 $pname 正在使用 [异火]-[NO.2]-[虚无吞炎] 请勿靠近！"),
				array(351,2,"351:2","§c玩家 $pname 正在使用 [异火]-[NO.3]-[净莲妖火] 请勿靠近！"),
				array(351,3,"351:3","§c玩家 $pname 正在使用 [异火]-[NO.4]-[金帝焚天炎] 请勿靠近！"),
				array(351,4,"351:4","§c玩家 $pname 正在使用 [异火]-[NO.5]-[生灵之焱] 提升能力！"),
				array(351,5,"351:5","§c玩家 $pname 正在使用 [异火]-[NO.6]-[八荒破灭焱] 请勿靠近！"),
				array(351,6,"351:6","§c玩家 $pname 正在使用 [异火]-[NO.7]-[九幽金祖火] 请勿靠近！"),
				array(351,7,"351:7","§c玩家 $pname 正在使用 [异火]-[NO.8]-[红莲妖火] 请勿靠近！"),
				array(351,8,"351:8","§c玩家 $pname 正在使用 [异火]-[NO.9]-[三千焱炎火] 请勿靠近！"),
				array(351,9,"351:9","§c玩家 $pname 正在使用 [异火]-[NO.10]-[九幽风炎] 请勿靠近！"),
				array(351,10,"351:10","§c玩家 $pname 正在使用 [异火]-[NO.11]-[骨灵冷火] 请勿靠近！")
			);	
			
			$rpg = array
			(	
				array(256,0,0,"§e玩家 $pname 正在使用 [武器]-[玄重尺] 请勿靠近！"),
				array(269,0,0,"§e玩家 $pname 正在使用 [武器]-[纳兰云芝剑] 请勿靠近！"),
				array(274,0,0,"§e玩家 $pname 正在使用 [武器]-[毒针] 请勿靠近！"),
				array(277,0,0,"§e玩家 $pname 正在使用 [武器]-[影刃] 请勿靠近！"),
				array(279,0,0,"§e玩家 $pname 正在使用 [武器]-[锁魂链] 请勿靠近！"),
				array(293,0,0,"§e玩家 $pname 正在使用 [武器]-[死神之镰] 请勿靠近！"),
			);

				$effect0=Effect::getEffect(Effect::SPEED);//速度 ID 【1】       [0]
				$effect1=Effect::getEffect(Effect::SLOWNESS);//缓慢 ID 【2】       [1] 
				$effect2=Effect::getEffect(Effect::HASTE);//急迫 ID 【3】       [2]
				$effect3=Effect::getEffect(Effect::HEALTH_BOOST);//提高生命力   [3]
				$effect4=Effect::getEffect(Effect::FATIGUE);//疲劳 ID【4】           [4]
				$effect5=Effect::getEffect(Effect::MINING_FATIGUE);//挖掘疲劳 ID【4】 [5]
				$effect6=Effect::getEffect(Effect::STRENGTH);//力量            [6]
				$effect7=Effect::getEffect(Effect::JUMP);//跳跃              [7]
				$effect8=Effect::getEffect(Effect::NAUSEA);//反胃   【9】         [8]
				$effect9=Effect::getEffect(Effect::CONFUSION);//混乱                [9]
				$effect10=Effect::getEffect(Effect::REGENERATION);//生命恢复 ID 【10】[10]
				$effect11=Effect::getEffect(Effect::DAMAGE_RESISTANCE);//抗性提升 【11 [11]
				$effect12=Effect::getEffect(Effect::FIRE_RESISTANCE);//防火【12】     [12]
				$effect13=Effect::getEffect(Effect::WATER_BREATHING);//水下呼吸【13】   [13]
				$effect14=Effect::getEffect(Effect::INVISIBILITY);//隐身 ID 【14】     [14]
				$effect15=Effect::getEffect(Effect::WEAKNESS);//虚弱 ID 【18】        [15]
				$effect16=Effect::getEffect(Effect::POISON);//中毒 ID 【19】           [16]
				$effect17=Effect::getEffect(Effect::WITHER);//凋谢                    [17]
				$effect18=Effect::getEffect(Effect::SWIFTNESS);		//  [18]

                 $eff = array
				(
					array("SPEED",0,6),//速度 ID 【1】       [0]
					array("SLOWNESS",1,10),//缓慢 ID 【2】       [1] 
					array("HASTE",2,14),//急迫 ID 【3】       [2]
					array("HEALTH_BOOST",3,18),//提高生命力   [3]
					array("FATIGUE",4,22),//疲劳 ID【4】           [4]
					array("MINING_FATIGUE",5,26),//挖掘疲劳 ID【4】 [5]
					array("STRENGTH",6,30),//力量            [6]
					array("JUMP",7,34),//跳跃              [7]
					array("NAUSEA",8,38),//反胃   【9】         [8]
					array("CONFUSION",9,42),//混乱                [9]
					array("REGENERATION",10,46),//生命恢复 ID 【10】[10]
					array("DAMAGE_RESISTANCE",11,50),//抗性提升 【11 [11]
					array("FIRE_RESISTANCE",12,54),//防火【12】     [12]
					array("WATER_BREATHING",13,60),//水下呼吸【13】   [13]
					array("INVISIBILITY",14,64),//隐身 ID 【14】     [14]
					array("WEAKNESS",15,68),//虚弱 ID 【18】        [15]
					array("POISON",16,72),//中毒 ID 【19】           [16]
					array("WITHER",17,76),//凋谢                    [17]
					array("SWIFTNESS",18,80)//  [18]
				);
			switch($damager->getInventory()->getItemInHand()->getID())
			{
				case $rpg[0][0]://玄重尺
					$entity->setHealth($entity->getHealth()-5);
					$effect6->setAmplifier($eff[2][1]);
					$effect6->setDuration(20*$eff[4][2]);
						$damager->addEffect($effect6);
					$effect1->setAmplifier($eff[1][1]);
					$effect1->setDuration(20*$eff[4][2]);
						$damager->addEffect($effect1);
				break;
				
				case $rpg[1][0]://纳兰云芝剑
					$entity->setHealth($entity->getHealth()-4);
					$effect0->setAmplifier($eff[1][1]);
					$effect0->setDuration(20*$eff[1][2]);
						$damager->addEffect($effect0);
				break;
				
				case $rpg[2][0]://毒针
					$entity->setHealth($entity->getHealth()-4);
					$effect16->setAmplifier($eff[2][1]);
					$effect16->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect16);
					$effect17->setAmplifier($eff[2][1]);
					$effect17->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect17);
				break;
				
				case $rpg[3][0]://影刃
					$entity->setHealth($entity->getHealth()-4);
					$effect0->setAmplifier($eff[2][1]);
					$effect0->setDuration(20*$eff[1][2]);
						$damager->addEffect($effect0);
					$effect8->setAmplifier($eff[2][1]);
					$effect8->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect8);
				break;
				
				case $rpg[4][0]://锁魂链
					$entity->setHealth($entity->getHealth()-4);
					$damager->setHealth($damager->getHealth()-1);
					$effect1->setAmplifier($eff[2][1]);
					$effect1->setDuration(20*$eff[1][2]);
						$damager->addEffect($effect1);
					$effect1->setAmplifier($eff[2][1]);
					$effect1->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect1);
					$effect15->setAmplifier($eff[3][1]);
					$effect15->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect15);
				break;
				
				case $rpg[5][0]://死神之镰
					$entity->setHealth($entity->getHealth()-6);
					$damager->setHealth($damager->getHealth()-1);
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[4][2]);
						$damager->addEffect($effect15);
					$effect8->setAmplifier($eff[2][1]);
					$effect8->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect8);
					$effect17->setAmplifier($eff[1][1]);
					$effect17->setDuration(20*$eff[1][2]);
						$entity->addEffect($effect17);
				break;
			}

						if($damager->getInventory()->getItemInHand()->getID() == $fire[0][0]){
							if(in_array($pname,$this->fire_players))
							{$damager->sendMessage("§4异火冷却中  ");}
						else{
							$this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"fire"],[$pname]),400);
							$this->fire_players[]=$pname;							
							$fd=$this->firedata;
						switch ($damage)
						{
							//-------异火
							case $fire[0][1]://帝炎
							if($fd->get($fire[0][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[0][3]);
								$effect1->setAmplifier($eff[4][1]);
								$effect1->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[4][1]);
								$effect15->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(11);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=101){
											if($else !== $damager)
											{
												$else->setOnFire(10);
											}
																													}
									}
								}
							}
							
					break;

							case $fire[1][1]://虚无吞炎
							if($fd->get($fire[1][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[1][3]);
								$effect1->setAmplifier($eff[3][1]);
								$effect1->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[3][1]);
								$effect15->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(10);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=65)
											if($else !== $damager)
											{
												$else->setOnFire(9);
												
											}
									}
								}
							}
							break;
							
							case $fire[2][1]://净莲妖火	
							if($fd->get($fire[2][1]) == $pname)
							{							
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[2][3]);
								$effect1->setAmplifier($eff[3][1]);
								$effect1->setDuration(20*$eff[0][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[3][1]);
								$effect15->setDuration(20*$eff[0][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(9);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=65)
											if($else !== $damager)
											{
												$else->setOnFire(8);
											}
									}
								}
							}	
							break;
							
							case $fire[3][1]://金帝焚天炎
							if($fd->get($fire[3][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[3][3]);
								$effect1->setAmplifier($eff[3][1]);
								$effect1->setDuration(20*$eff[0][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[3][1]);
								$effect15->setDuration(20*$eff[0][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(8);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=65)
											if($else !== $damager)
											{
												$else->setOnFire(7);
											}
									}
								}
							}
							break;
				
							case $fire[4][1]://生灵之焱
							if($fd->get($fire[4][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[4][3]);
								$effect10->setAmplifier($eff[2][1]);
								$effect10->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect10);
								$effect11->setAmplifier($eff[2][1]);
								$effect11->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect11);
								$effect3->setAmplifier($eff[2][1]);
								$effect3->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect3);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=50)
											if($else !== $damager)
											{
								$effect10->setAmplifier($eff[2][1]);
								$effect10->setDuration(20*$eff[0][2]);
									$else->addEffect($effect10);
											}
									}
								}
							}
							break;
							
							case $fire[5][1]://八荒破灭焱
							if($fd->get($fire[5][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[5][3]);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[4][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[4][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(7);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=50)
											if($else !== $damager)
											{
												$else->setOnFire(6);
											}
									}
								}
							}
							break;
							
							case $fire[6][1]://九幽金祖火
							if($fd->get($fire[6][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[6][3]);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[3][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[3][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(6);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=50)
											if($else !== $damager)
											{
												$else->setOnFire(5);
											}
									}
								}
							}
							break;
						
							case $fire[7][1]://红莲妖火
							if($fd->get($fire[7][1]) == $pname)
							{							
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[7][3]);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[2][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[2][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(5);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=50)
											if($else !== $damager)
											{
												$else->setOnFire(4);
											}
									}
								}
							}
							break;
							
							case $fire[8][1]://三千焱炎火
							if($fd->get($fire[8][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[8][3]);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect1);
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(4);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=37)
											if($else !== $damager)
											{
												$else->setOnFire(3);
											}
									}
								}
							}
							break;

							case $fire[9][1]://九幽风炎
							if($fd->get($fire[9][1]) == $pname)
							{
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[9][3]);
								$effect16->setAmplifier($eff[2][1]);
								$effect16->setDuration(20*$eff[2][2]);
									$entity->addEffect($effect16);
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[2][2]);
									$entity->addEffect($effect15);
								$entity->setOnFire(3);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=37)
											if($else !== $damager)
											{
												$else->setOnFire(2);
											}
									}
								}
							}
							break;
							
							case $fire[10][1]://骨灵冷火
							if($fd->get($fire[10][1]) == $pname)
							{							
							$this->getServer()->broadcastMessage(TextFormat::BLUE .$fire[10][3]);
								$effect16->setAmplifier($eff[2][1]);
								$effect16->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect16);
								$effect9->setAmplifier($eff[2][1]);
								$effect9->setDuration(20*$eff[1][2]);
									$entity->addEffect($effect9);
								$entity->setOnFire(2);
								foreach($this->getServer()->getOnlinePlayers() as $else)
								{
									if($else instanceof Player)
									{
										if($else->distance(new Vector3($damager->x,$damager->y,$damager->z))<=37)
											if($else !== $damager)
											{
												$else->setOnFire(1);
											}
									}
								}
							}
							break;
						
						}
					}
               		}
      			}
      		}
      	}
      		
		}
		}
		}
//--------------------消耗物品
   		public function onInteract(PlayerInteractEvent $event)
		{

   			$player=$event->getPlayer();
   			$pname=$player->getName();
   			$sign=$event->getBlock();
			//技能 RED .攻击   GREEN .恢复  YELLOW .提升  BLUE .
				 $gift = array
				 (
					array(263,0,0,"玩家 $pname 正在使用 [地阶低级]-[焰分噬浪尺] 请勿靠近！"),
					array(262,0,0,"玩家 $pname 正在使用 [地阶中级]-[六合游身尺] 提升能力！"),
					array(265,0,0,"玩家 $pname 正在使用 [地阶高级]-[帝印诀] 请勿靠近！"),
					array(266,0,0,"玩家 $pname 正在使用 [玄阶高级]-[八极崩] 请勿靠近！"),
					array(264,0,0,"玩家 $pname 正在使用 [飞行斗技]-[雁飞九天行] 提升能力！"),
					array(280,0,0,"玩家 $pname 正在使用 [自创斗技]-[怒火佛莲] 请勿靠近！"),
					array(281,0,0,"玩家 $pname 正在使用 [自创斗技]-[毁灭佛莲] 请勿靠近！"),
					array(287,0,0,"玩家 $pname 正在使用 [天阶低级]-[五轮离火法] 请勿靠近！"),
					array(289,0,0,"玩家 $pname 正在使用 [地阶低级]-[三千雷动] 提升能力！"),
					array(295,0,0,"玩家 $pname 正在使用 [低阶高级]-[三千雷幻身] 提升能力！"),
					array(296,0,0,"玩家 $pname 正在使用 [防身之技]-[龙凰古甲] 提升能力！"),
					array(297,0,0,"玩家 $pname 正在使用 [低阶高级]-[大天造化掌] 请勿靠近！"),
					array(318,0,0,"玩家 $pname 正在使用 [天阶低级]-[金刚琉璃身] 提升能力！"),
					array(319,0,0,"玩家 $pname 正在使用 [自创斗技]-[佛怒轮回] 请勿靠近！"),
					array(320,0,0,"玩家 $pname 正在使用 [小伊附体]-[毁灭火体] 提升能力！"),
					array(331,0,0,"玩家 $pname 正在使用 [先天毒体]-[厄难毒体] 请勿靠近！"),
					array(338,0,0,"玩家 $pname 正在使用 [先天体质]-[碧眼三花瞳] 请勿靠近！")
				 );
				 $gift1=array($gift[0][0],$gift[1][0],$gift[2][0],$gift[3][0],$gift[4][0],$gift[5][0],$gift[6][0],$gift[7][0],$gift[8][0],$gift[9][0],$gift[10][0],$gift[11][0],$gift[12][0],$gift[13][0],$gift[14][0],$gift[15][0],$gift[16][0]);
				 //效果
                 $eff = array
				(
					array("SPEED",0,6),//速度 ID 【1】       [0]
					array("SLOWNESS",1,10),//缓慢 ID 【2】       [1] 
					array("HASTE",2,14),//急迫 ID 【3】       [2]
					array("HEALTH_BOOST",3,18),//提高生命力   [3]
					array("FATIGUE",4,22),//疲劳 ID【4】           [4]
					array("MINING_FATIGUE",5,26),//挖掘疲劳 ID【4】 [5]
					array("STRENGTH",6,30),//力量            [6]
					array("JUMP",7,34),//跳跃              [7]
					array("NAUSEA",8,38),//反胃   【9】         [8]
					array("CONFUSION",9,42),//混乱                [9]
					array("REGENERATION",10,46),//生命恢复 ID 【10】[10]
					array("DAMAGE_RESISTANCE",11,50),//抗性提升 【11 [11]
					array("FIRE_RESISTANCE",12,54),//防火【12】     [12]
					array("WATER_BREATHING",13,60),//水下呼吸【13】   [13]
					array("INVISIBILITY",14,64),//隐身 ID 【14】     [14]
					array("WEAKNESS",15,68),//虚弱 ID 【18】        [15]
					array("POISON",16,72),//中毒 ID 【19】           [16]
					array("WITHER",17,76),//凋谢                    [17]
					array("SWIFTNESS",18,80)//  [18]
				);
				$effect0=Effect::getEffect(Effect::SPEED);//速度 ID 【1】       [0]
				$effect1=Effect::getEffect(Effect::SLOWNESS);//缓慢 ID 【2】       [1] 
				$effect2=Effect::getEffect(Effect::HASTE);//急迫 ID 【3】       [2]
				$effect3=Effect::getEffect(Effect::HEALTH_BOOST);//提高生命力   [3]
				$effect4=Effect::getEffect(Effect::FATIGUE);//疲劳 ID【4】           [4]
				$effect5=Effect::getEffect(Effect::MINING_FATIGUE);//挖掘疲劳 ID【4】 [5]
				$effect6=Effect::getEffect(Effect::STRENGTH);//力量            [6]
				$effect7=Effect::getEffect(Effect::JUMP);//跳跃              [7]
				$effect8=Effect::getEffect(Effect::NAUSEA);//反胃   【9】         [8]
				$effect9=Effect::getEffect(Effect::CONFUSION);//混乱                [9]
				$effect10=Effect::getEffect(Effect::REGENERATION);//生命恢复 ID 【10】[10]
				$effect11=Effect::getEffect(Effect::DAMAGE_RESISTANCE);//抗性提升 【11 [11]
				$effect12=Effect::getEffect(Effect::FIRE_RESISTANCE);//防火【12】     [12]
				$effect13=Effect::getEffect(Effect::WATER_BREATHING);//水下呼吸【13】   [13]
				$effect14=Effect::getEffect(Effect::INVISIBILITY);//隐身 ID 【14】     [14]
				$effect15=Effect::getEffect(Effect::WEAKNESS);//虚弱 ID 【18】        [15]
				$effect16=Effect::getEffect(Effect::POISON);//中毒 ID 【19】           [16]
				$effect17=Effect::getEffect(Effect::WITHER);//凋谢                    [17]
				$effect18=Effect::getEffect(Effect::SWIFTNESS);		//  [18]
					if(in_array($player->getInventory()->getItemInHand()->getID(),$gift1) && $this->canPVP($player->getLevel())){
				if(in_array($pname,$this->gift_players)){$player->sendMessage("§4[FightTheSky] : 斗技冷却中 .");}
				else{
					$this->getServer()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"gift"],[$pname]),300);
					$this->gift_players[] =$pname;
			switch ($player->getInventory()->getItemInHand()->getID())
			{
				case $gift[0][0]://$gift[0][0]://焰分噬浪尺

					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[0][3] ); 
					$player->getInventory()->removeItem(new Item($gift[0][0],$gift[0][1],1));
							$player->setHealth($player->getHealth()-1);
							$effect15->setVisible(true);
							$effect15->setAmplifier($eff[1][1]);
							$effect15->setDuration(20*$eff[5][2]);
								$player->addEffect($effect15);	
				foreach ($this->getServer()->getOnlinePlayers() as $else)
				{
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=26)
						{
							if($else !== $player)
							{
							$else->setHealth($else->getHealth()-3);
							$effect2->setVisible(true);
							$effect2->setAmplifier($eff[1][1]);
							$effect2->setDuration(20*$eff[5][2]);
								$else->addEffect($effect2);
							$effect1->setVisible(true);
							$effect1->setAmplifier($eff[1][1]);
							$effect1->setDuration(20*$eff[5][2]);
								$else->addEffect($effect1);						
							
							}							
						}

					}
				}
				
				break;
				
				case $gift[1][0]://六合游身尺

					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[1][3]); 
					$player->getInventory()->removeItem(new Item($gift[1][0],$gift[1][1],1));
							$effect1->setVisible(true);
							$effect1->setAmplifier($eff[1][1]);
							$effect1->setDuration(20*$eff[4][2]);
								$player->addEffect($effect1);
							$effect6->setVisible(true);
							$effect6->setAmplifier($eff[1][1]);
							$effect6->setDuration(20*$eff[3][2]);
								$player->addEffect($effect6);
							$effect11->setVisible(true);
							$effect11->setAmplifier($eff[1][1]);
							$effect11->setDuration(20*$eff[5][2]);
								$player->addEffect($effect11);						
				break;
				
				case $gift[2][0]://帝印诀
                    $this->getServer()->broadcastMessage(TextFormat::RED .$gift[2][3]);
					$player->getInventory()->removeItem(new Item(352,$gift[2][1],1));
							$effect15->setAmplifier($eff[2][1]);
							$effect15->setDuration(20*$eff[1][2]);
								$player->addEffect($effect15);	
					foreach ($this->getServer()->getOnlinePlayers() as $else){
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=65)
						{
							if($else !== $player)
							{
								$else->setHealth($else->getHealth()-4);
							$effect2->setAmplifier($eff[2][1]);
							$effect2->setDuration(20*$eff[1][2]);
								$else->addEffect($effect2);
							$effect8->setAmplifier($eff[2][1]);
							$effect8->setDuration(20*$eff[1][2]);
								$else->addEffect($effect8);							
								
							}
						}

					}
				}
				break;
				
				case $gift[3][0]:// 八极崩

					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[3][3]);
					$player->getInventory()->removeItem(new Item($gift[3][0],$gift[3][1],1));
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[1][2]);
								$player->addEffect($effect15);
					foreach ($this->getServer()->getOnlinePlayers() as $else){
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=5)
						{
							if($else !== $player)
							{
								$else->setHealth($else->getHealth()-5);
								$effect8->setAmplifier($eff[2][1]);
								$effect8->setDuration(20*$eff[1][2]);
									$else->addEffect($effect8);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[1][2]);
									$else->addEffect($effect1);
							}
						}
					}
					
				}
				break;
				
				case $gift[4][0]:// 雁飞九天行
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[4][3]);
					$player->getInventory()->removeItem(new Item($gift[4][0],$gift[4][1],1));
					$effect0->setAmplifier($eff[2][1]);
					$effect0->setDuration(20*$eff[4][2]);
						$player->addEffect($effect0);
					$effect11->setAmplifier($eff[2][1]);
					$effect11->setDuration(20*$eff[1][2]);
						$player->addEffect($effect11);
					$effect6->setAmplifier($eff[1][1]);
					$effect6->setDuration(20*$eff[4][2]);
						$player->addEffect($effect6);					
				break;

				case $gift[5][0]:// 怒火佛莲
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[5][3]);
					$player->getInventory()->removeItem(new Item($gift[5][0],$gift[5][1],1));
					$player->setHealth($player->getHealth()-1);
					$effect15->setAmplifier($eff[1][1]);
					$effect15->setDuration(20*$eff[4][2]);
						$player->addEffect($effect15);
					foreach ($this->getServer()->getOnlinePlayers() as $else){
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=26)
						{
							if($else !== $player)
							{
								$else->setHealth($else->getHealth()-3);
								$effect15->setAmplifier($eff[3][1]);
								$effect15->setDuration(20*$eff[4][2]);
									$else->addEffect($effect15);
								$else->setOnFire(5);
							}
						}
					}
					
				}
				break;
				
				case $gift[6][0]:// 毁灭佛莲
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[6][3]);
					$player->getInventory()->removeItem(new Item($gift[6][0],$gift[6][1],1));
					$player->setHealth($player->getHealth()-2);
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[1][2]);
						$player->addEffect($effect15);
					foreach ($this->getServer()->getOnlinePlayers() as $else){
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=101)
						{
							if($else !== $player){
							$else->setHealth($else->getHealth()-5);
							$else->setOnFire(6);
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[4][2]);
						$else->addEffect($effect15);
					$effect1->setAmplifier($eff[2][1]);
					$effect1->setDuration(20*$eff[4][2]);
						$else->addEffect($effect1);
							}						
						}
					}
				}	
				break;

				case $gift[7][0]:// 五轮离火法
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[7][3]);
					$player->getInventory()->removeItem(new Item($gift[7][0],$gift[7][1],1));
					$player->setHealth($player->getHealth()-2.5);
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[1][2]);
						$player->addEffect($effect15);
				foreach ($this->getServer()->getOnlinePlayers() as $else){
					if($else instanceof Player)
					{
						if($else->distance(new Vector3($player->x,$player->y,$player->z))<=101)
						{
							if($else !== $player)
							{
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[4][2]);
						$else->addEffect($effect15);
					$effect1->setAmplifier($eff[2][1]);
					$effect1->setDuration(20*$eff[4][2]);
						$else->addEffect($effect1);
					$else->setOnFire(7);
							}
						}
					}
				}	
				break;

				case $gift[8][0]:// 三千雷动
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[8][3]);
					$player->getInventory()->removeItem(new Item($gift[8][0],$gift[8][1],1));
					$effect0->setAmplifier($eff[3][1]);
					$effect0->setDuration(20*$eff[4][2]);
						$player->addEffect($effect0);
					$effect7->setAmplifier($eff[2][1]);
					$effect7->setDuration(20*$eff[4][2]);
						$player->addEffect($effect7);
					$effect6->setAmplifier($eff[2][1]);
					$effect6->setDuration(20*$eff[4][2]);
						$player->addEffect($effect6);						
				break;

				case $gift[9][0]:// 三千雷幻身
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[9][3]);
					$player->getInventory()->removeItem(new Item($gift[9][0],$gift[9][1],1));
					$effect0->setAmplifier($eff[4][1]);
					$effect0->setDuration(20*$eff[4][2]);
						$player->addEffect($effect0);
					$effect7->setAmplifier($eff[3][1]);
					$effect7->setDuration(20*$eff[4][2]);
						$player->addEffect($effect7);
					$effect6->setAmplifier($eff[3][1]);
					$effect6->setDuration(20*$eff[4][2]);
						$player->addEffect($effect6);							
				break;

				case $gift[10][0]:// 龙凰古甲
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[10][3]);
					$player->getInventory()->removeItem(new Item($gift[10][0],$gift[10][1],1));
					$effect10->setAmplifier($eff[4][1]);
					$effect10->setDuration(20*$eff[3][2]);
						$player->addEffect($effect10);
					$effect3->setAmplifier($eff[3][1]);
					$effect3->setDuration(20*$eff[3][2]);
						$player->addEffect($effect3);
					$effect6->setAmplifier($eff[3][1]);
					$effect6->setDuration(20*$eff[3][2]);
						$player->addEffect($effect6);		
				break;

				case $gift[11][0]:// 大天造化掌
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[11][3]);
					$player->getInventory()->removeItem(new Item($gift[11][0],$gift[11][1],1));
					foreach ($this->getServer()->getOnlinePlayers() as $else)
					{
						if($else instanceof Player)
						{
							if($else->distance(new Vector3($player->x,$player->y,$player->z))<=101)
							{
								if($else !== $player)
								{
								$else->setHealth($else->getHealth()-5);
					$effect8->setAmplifier($eff[2][1]);
					$effect8->setDuration(20*$eff[1][2]);
						$else->addEffect($effect8);
					$effect15->setAmplifier($eff[2][1]);
					$effect15->setDuration(20*$eff[1][2]);
						$else->addEffect($effect15);
					$effect1->setAmplifier($eff[2][1]);
					$effect1->setDuration(20*$eff[1][2]);
						$else->addEffect($effect1);		
								}
							}
						}
					}					
					
				break;				
				
				
				case $gift[12][0]:// 金刚琉璃身
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[12][3]);
					$player->getInventory()->removeItem(new Item($gift[12][0],$gift[12][1],1));
					$effect12->setAmplifier($eff[2][1]);
					$effect12->setDuration(20*$eff[4][2]);
						$player->addEffect($effect12);
					$effect6->setAmplifier($eff[6][1]);
					$effect6->setDuration(20*$eff[3][2]);
						$player->addEffect($effect6);
					$effect0->setAmplifier($eff[2][1]);
					$effect0->setDuration(20*$eff[3][2]);
						$player->addEffect($effect0);	
					$effect3->setAmplifier($eff[2][1]);
					$effect3->setDuration(20*$eff[4][2]);
						$player->addEffect($effect3);							
					
				break;

				case $gift[13][0]:// 佛怒轮回
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[13][3]);
					$player->getInventory()->removeItem(new Item($gift[13][0],$gift[13][1],1));
					$player->setHealth($player->getHealth()-2);
					foreach ($this->getServer()->getOnlinePlayers() as $else)
					{
						if($else instanceof Player)
						{
							if($else->distance(new Vector3($player->x,$player->y,$player->z))<=225)
							{
								if($else !== $player)
								{
									$else->setHealth($else->getHealth()-6);
									$else->setOnFire(8);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[1][2]);
									$else->addEffect($effect1);	
								$effect15->setAmplifier($eff[2][1]);
								$effect15->setDuration(20*$eff[1][2]);
									$else->addEffect($effect15);	
								$effect17->setAmplifier($eff[2][1]);
								$effect17->setDuration(20*$eff[1][2]);
									$else->addEffect($effect17);	
								}
							}
						}
					}					
					
				break;
				
				case $gift[14][0]:// 毁灭火体
					$this->getServer()->broadcastMessage(TextFormat::YELLOW .$gift[14][3]);
					$player->getInventory()->removeItem(new Item($gift[14][0],$gift[14][1],1));
								$effect6->setAmplifier($eff[4][1]);
								$effect6->setDuration(20*$eff[6][2]);
									$player->addEffect($effect6);							
								$effect12->setAmplifier($eff[4][1]);
								$effect12->setDuration(20*$eff[6][2]);
									$player->addEffect($effect12);	
								$effect0->setAmplifier($eff[3][1]);
								$effect0->setDuration(20*$eff[6][2]);
									$player->addEffect($effect0);	
								$effect7->setAmplifier($eff[3][1]);
								$effect7->setDuration(20*$eff[6][2]);
									$player->addEffect($effect7);	
								$effect11->setAmplifier($eff[3][1]);
								$effect11->setDuration(20*$eff[6][2]);
									$player->addEffect($effect11);										
				break;

				case $gift[15][0]:// 厄难毒体
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[15][3]);
					$player->getInventory()->removeItem(new Item($gift[15][0],$gift[15][1],1));
					$player->setHealth($player->getHealth()-2);
								$effect15->setAmplifier($eff[1][1]);
								$effect15->setDuration(20*$eff[1][2]);
									$player->addEffect($effect15);					
					foreach ($this->getServer()->getOnlinePlayers() as $else)
					{
						if($else instanceof Player)
						{
							if($else->distance(new Vector3($player->x,$player->y,$player->z))<=26)
							{
								if($else !== $player)
								{
									$else->setHealth($else->getHealth()-1.5);
								$effect16->setAmplifier($eff[4][1]);
								$effect16->setDuration(20*$eff[1][2]);
									$else->addEffect($effect16);
								$effect15->setAmplifier($eff[3][1]);
								$effect15->setDuration(20*$eff[6][2]);
									$else->addEffect($effect15);
								$effect1->setAmplifier($eff[2][1]);
								$effect1->setDuration(20*$eff[4][2]);
									$else->addEffect($effect1);
								$effect17->setAmplifier($eff[2][1]);
								$effect17->setDuration(20*$eff[1][2]);
									$else->addEffect($effect17);
								}
							}
						}
					}					
					
				break;
				
				case $gift[16][0]:// 碧眼三花瞳
					$this->getServer()->broadcastMessage(TextFormat::RED .$gift[16][3]);
					$player->getInventory()->removeItem(new Item($gift[16][0],$gift[16][1],1));
								$effect14->setAmplifier($eff[2][1]);
								$effect14->setDuration(20*$eff[4][2]);
									$player->addEffect($effect14);
								$effect10->setAmplifier($eff[2][1]);
								$effect10->setDuration(20*$eff[1][2]);
									$player->addEffect($effect10);									
					foreach ($this->getServer()->getOnlinePlayers() as $else)
					{
						if($else instanceof Player)
						{
							if($else->distance(new Vector3($player->x,$player->y,$player->z))<=26)
							{
								if($else !== $player)
								{
								$effect4->setAmplifier($eff[2][1]);
								$effect4->setDuration(20*$eff[1][2]);
									$else->addEffect($effect4);										
								$effect8->setAmplifier($eff[2][1]);
								$effect8->setDuration(20*$eff[1][2]);
									$else->addEffect($effect8);											
								}
							}
						}
					}					
					
				break;
			}
		 }
		}
   		}

//--------------------广播
   		    public function onHeld(PlayerItemHeldEvent $event)
			   {   		    
   		       	$player=$event->getPlayer();
   		       	$pname=$player->getName();
   		       	$item=$event->getItem()->getID(); 
				$damage=$event->getItem()->getDamage();
				$fd=$this->firedata;
		       	 //数组 [效果]-[强度]-[时间]
                 $eff = array
				(
					array("SPEED",0,6),//速度 ID 【1】       [0]
					array("SLOWNESS",1,10),//缓慢 ID 【2】       [1] 
					array("HASTE",2,14),//急迫 ID 【3】       [2]
					array("HEALTH_BOOST",3,18),//提高生命力   [3]
					array("FATIGUE",4,22),//疲劳 ID【4】           [4]
					array("MINING_FATIGUE",5,26),//挖掘疲劳 ID【4】 [5]
					array("STRENGTH",6,30),//力量            [6]
					array("JUMP",7,34),//跳跃              [7]
					array("NAUSEA",8,38),//反胃   【9】         [8]
					array("CONFUSION",9,42),//混乱                [9]
					array("REGENERATION",10,46),//生命恢复 ID 【10】[10]
					array("DAMAGE_RESISTANCE",11,50),//抗性提升 【11 [11]
					array("FIRE_RESISTANCE",12,54),//防火【12】     [12]
					array("WATER_BREATHING",13,60),//水下呼吸【13】   [13]
					array("INVISIBILITY",14,64),//隐身 ID 【14】     [14]
					array("WEAKNESS",15,68),//虚弱 ID 【18】        [15]
					array("POISON",16,72),//中毒 ID 【19】           [16]
					array("WITHER",17,76),//凋谢                    [17]
					array("SWIFTNESS",18,80)//  [18]
				);
				$effect0=Effect::getEffect(Effect::SPEED);//速度 ID 【1】       [0]
				$effect1=Effect::getEffect(Effect::SLOWNESS);//缓慢 ID 【2】       [1] 
				$effect2=Effect::getEffect(Effect::HASTE);//急迫 ID 【3】       [2]
				$effect3=Effect::getEffect(Effect::HEALTH_BOOST);//提高生命力   [3]
				$effect4=Effect::getEffect(Effect::FATIGUE);//疲劳 ID【4】           [4]
				$effect5=Effect::getEffect(Effect::MINING_FATIGUE);//挖掘疲劳 ID【4】 [5]
				$effect6=Effect::getEffect(Effect::STRENGTH);//力量            [6]
				$effect7=Effect::getEffect(Effect::JUMP);//跳跃              [7]
				$effect8=Effect::getEffect(Effect::NAUSEA);//反胃   【9】         [8]
				$effect9=Effect::getEffect(Effect::CONFUSION);//混乱                [9]
				$effect10=Effect::getEffect(Effect::REGENERATION);//生命恢复 ID 【10】[10]
				$effect11=Effect::getEffect(Effect::DAMAGE_RESISTANCE);//抗性提升 【11 [11]
				$effect12=Effect::getEffect(Effect::FIRE_RESISTANCE);//防火【12】     [12]
				$effect13=Effect::getEffect(Effect::WATER_BREATHING);//水下呼吸【13】   [13]
				$effect14=Effect::getEffect(Effect::INVISIBILITY);//隐身 ID 【14】     [14]
				$effect15=Effect::getEffect(Effect::WEAKNESS);//虚弱 ID 【18】        [15]
				$effect16=Effect::getEffect(Effect::POISON);//中毒 ID 【19】           [16]
				$effect17=Effect::getEffect(Effect::WITHER);//凋谢                    [17]
				$effect18=Effect::getEffect(Effect::SWIFTNESS);		//  [18]
			//斗技
				 $gift = array
				 (
					array(263,0,0,"§4 [地阶低级]-[焰分噬浪尺] \n长按使用 ！"),
					array(262,0,0,"§e [地阶中级]-[六合游身尺] \n长按使用 ！"),
					array(265,0,0,"§4 [地阶高级]-[帝印诀] \n长按使用 ！"),
					array(266,0,0,"§4 [玄阶高级]-[八极崩] \n长按使用 ！"),
					array(264,0,0,"§e [飞行斗技]-[雁飞九天行] \n长按使用 ！"),
					array(280,0,0,"§4 [自创斗技]-[怒火佛莲] \n长按使用 ！"),
					array(281,0,0,"§4 [自创斗技]-[毁灭佛莲] \n长按使用 ！"),
					array(287,0,0,"§4 [天阶低级]-[五轮离火法] \n长按使用 ！"),
					array(289,0,0,"§e [地阶低级]-[三千雷动] \n长按使用 ！"),
					array(295,0,0,"§e [低阶高级]-[三千雷幻身] \n长按使用 ！"),
					array(296,0,0,"§e [防身之技]-[龙凰古甲] \n长按使用 ！"),
					array(297,0,0,"§4 [低阶高级]-[大天造化掌] \n长按使用 ！"),
					array(318,0,0,"§e [天阶低级]-[金刚琉璃身] \n长按使用 ！"),
					array(319,0,0,"§4 [自创斗技]-[佛怒轮回] \n长按使用 ！"),
					array(320,0,0,"§e [小伊附体]-[毁灭火体] \n长按使用 ！"),
					array(331,0,0,"§4 [先天毒体]-[厄难毒体] \n长按使用 ！"),
					array(338,0,0,"§4 [先天体质]-[碧眼三花瞳] \n长按使用 ！")
				 );
				switch($item)
				{
					case $gift[0][0]:
					$player->sendTip($gift[0][3]);
					break;
					case $gift[1][0]:
					$player->sendTip($gift[1][3]);
					break;
					case $gift[2][0]:
					$player->sendTip($gift[2][3]);
					break;
					case $gift[3][0]:
					$player->sendTip($gift[3][3]);
					break;
					case $gift[4][0]:
					$player->sendTip($gift[4][3]);
					break;
					case $gift[5][0]:
					$player->sendTip($gift[5][3]);
					break;
					case $gift[6][0]:
					$player->sendTip($gift[6][3]);
					break;
					case $gift[7][0]:
					$player->sendTip($gift[7][3]);
					break;
					case $gift[8][0]:
					$player->sendTip($gift[8][3]);
					break;
					case $gift[9][0]:
					$player->sendTip($gift[9][3]);
					break;
					case $gift[10][0]:
					$player->sendTip($gift[10][3]);
					break;
					case $gift[11][0]:
					$player->sendTip($gift[11][3]);
					break;
					case $gift[12][0]:
					$player->sendTip($gift[12][3]);
					break;
					case $gift[13][0]:
					$player->sendTip($gift[13][3]);
					break;
					case $gift[14][0]:
					$player->sendTip($gift[14][3]);
					break;
					case $gift[15][0]:
					$player->sendTip($gift[15][3]);
					break;
					case $gift[16][0]:
					$player->sendTip($gift[16][3]);
					break;
				}




				 
			$rpg = array
			(	
				array(256,0,0,"§b [武器]-[玄重尺]"),
				array(269,0,0,"§b [武器]-[纳兰云芝剑]"),
				array(274,0,0,"§b [武器]-[毒针]"),
				array(277,0,0,"§b [武器]-[影刃]"),
				array(279,0,0,"§b [武器]-[锁魂链]"),
				array(293,0,0,"§b [武器]-[死神之镰]"),
			);
			
			//武器
			switch ($item)
			{
					case $rpg[0][0]:
					$player->sendTip($rpg[0][3]);
					break;
			
					case $rpg[1][0]:
					$player->sendTip($rpg[1][3]);
					break;
					
					case $rpg[2][0]:
					$player->sendTip($rpg[2][3]);
					break;
					
					case $rpg[3][0]:
					$player->sendTip($rpg[3][3]);
					break;
					
					case $rpg[4][0]:
					$player->sendTip($rpg[4][3]);
					break;
					
					case $rpg[5][0]:
					$player->sendTip($rpg[5][3]);
					break;
			}
			
			
			$fire = array
			(
				array(351,0,"351:0","§3 [异火]-[NO.1]-[帝炎] \n§f 点击攻击 ！"),
				array(351,1,"351:1","§3 [异火]-[NO.2]-[虚无吞炎] \n§f 点击攻击 ！"),
				array(351,2,"351:2","§3 [异火]-[NO.3]-[净莲妖火] \n§f 点击攻击 ！"),
				array(351,3,"351:3","§3 [异火]-[NO.4]-[金帝焚天炎] \n§f 点击攻击 ！"),
				array(351,4,"351:4","§3 [异火]-[NO.5]-[生灵之焱] \n§f 点击攻击 ！"),
				array(351,5,"351:5","§3 [异火]-[NO.6]-[八荒破灭焱] \n§f 点击攻击 ！"),
				array(351,6,"351:6","§3 [异火]-[NO.7]-[九幽金祖火]\n§f 点击攻击 ！"),
				array(351,7,"351:7","§3 [异火]-[NO.8]-[红莲妖火] \n§f点击攻击 ！"),
				array(351,8,"351:8","§3 [异火]-[NO.9]-[三千焱炎火] \n§f 点击攻击 ！"),
				array(351,9,"351:9","§3 [异火]-[NO.10]-[九幽风炎] \n§f 点击攻击 ！"),
				array(351,10,"351:10","§3 [异火]-[NO.11]-[骨灵冷火] \n§f 点击攻击 ！")
			);
			switch($item)
			{
				case 246:
				return $event;
				break;
			}
			
			//异火
			if($item==$fire[0][0]){
				switch($damage)
				{
					case $fire[0][1]:
							if($fd->get($fire[0][1]) == $pname)
							{
						$effect12->setAmplifier($eff[4][1]);
						$effect12->setDuration(20*$eff[4][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[4][1]);
						$effect6->setDuration(20*$eff[4][2]);
							$player->addEffect($effect6);
						$effect3->setAmplifier($eff[4][1]);
						$effect3->setDuration(20*$eff[4][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[4][1]);
						$effect11->setDuration(20*$eff[4][2]);
							$player->addEffect($effect11);
						$player->sendTip($fire[0][3]);
							}
					break;
					
					case $fire[1][1]:
							if($fd->get($fire[1][1]) == $pname)
							{
						$effect12->setAmplifier($eff[3][1]);
						$effect12->setDuration(20*$eff[4][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[3][1]);
						$effect6->setDuration(20*$eff[4][2]);
							$player->addEffect($effect6);
						$effect3->setAmplifier($eff[3][1]);
						$effect3->setDuration(20*$eff[4][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[3][1]);
						$effect11->setDuration(20*$eff[4][2]);
							$player->addEffect($effect11);
						$player->sendTip($fire[1][3]);
							}
					break;
					
					case $fire[2][1]:
							if($fd->get($fire[2][1]) == $pname)
							{
						$effect12->setAmplifier($eff[3][1]);
						$effect12->setDuration(20*$eff[3][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[3][1]);
						$effect6->setDuration(20*$eff[3][2]);
							$player->addEffect($effect6);
						$effect0->setAmplifier($eff[3][1]);
						$effect0->setDuration(20*$eff[3][2]);
							$player->addEffect($effect0);
						$effect3->setAmplifier($eff[3][1]);
						$effect3->setDuration(20*$eff[3][2]);
							$player->addEffect($effect3);
						$player->sendTip($fire[2][3]);
							}
					break;
					
					case $fire[3][1]:
							if($fd->get($fire[0][1]) == $pname)
							{
						$effect12->setAmplifier($eff[3][1]);
						$effect12->setDuration(20*$eff[2][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[3][1]);
						$effect6->setDuration(20*$eff[2][2]);
							$player->addEffect($effect6);
						$effect7->setAmplifier($eff[3][1]);
						$effect7->setDuration(20*$eff[2][2]);
							$player->addEffect($effect7);
						$effect0->setAmplifier($eff[3][1]);
						$effect0->setDuration(20*$eff[2][2]);
							$player->addEffect($effect0);
						$player->sendTip($fire[3][3]);
							}
					break;
					
					case $fire[4][1]:
							if($fd->get($fire[0][1]) == $pname)
							{
						$effect12->setAmplifier($eff[3][1]);
						$effect12->setDuration(20*$eff[1][2]);
							$player->addEffect($effect12);
						$effect10->setAmplifier($eff[3][1]);
						$effect10->setDuration(20*$eff[1][2]);
							$player->addEffect($effect10);
						$effect11->setAmplifier($eff[3][1]);
						$effect11->setDuration(20*$eff[1][2]);
							$player->addEffect($effect11);
						$effect3->setAmplifier($eff[3][1]);
						$effect3->setDuration(20*$eff[1][2]);
							$player->addEffect($effect3);
						$player->sendTip($fire[4][3]);
							}
					
					break;
					
					case $fire[5][1]:
							if($fd->get($fire[5][1]) == $pname)
							{
						$effect12->setAmplifier($eff[3][1]);
						$effect12->setDuration(20*$eff[0][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[3][1]);
						$effect6->setDuration(20*$eff[0][2]);
							$player->addEffect($effect6);
						$effect1->setAmplifier($eff[3][1]);
						$effect11->setDuration(20*$eff[0][2]);
							$player->addEffect($effect11);
						$effect3->setAmplifier($eff[3][1]);
						$effect3->setDuration(20*$eff[0][2]);
							$player->addEffect($effect3);
						$player->sendTip($fire[5][3]);
							}
					break;
					
					case $fire[6][1]:
							if($fd->get($fire[6][1]) == $pname)
							{
						$effect12->setAmplifier($eff[2][1]);
						$effect12->setDuration(20*$eff[4][2]);
							$player->addEffect($effect12);
						$effect6->setAmplifier($eff[2][1]);
						$effect6->setDuration(20*$eff[4][2]);
							$player->addEffect($effect6);
						$effect3->setAmplifier($eff[2][1]);
						$effect3->setDuration(20*$eff[4][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[2][1]);
						$effect11->setDuration(20*$eff[4][2]);
							$player->addEffect($effect11);
						$player->sendTip($fire[6][3]);
							}
					break;
					
					case $fire[7][1]:
							if($fd->get($fire[0][1]) == $pname)
							{
						$effect12->setAmplifier($eff[2][1]);
						$effect12->setDuration(20*$eff[3][2]);
							$player->addEffect($effect12);
						$effect3->setAmplifier($eff[2][1]);
						$effect3->setDuration(20*$eff[3][2]);
							$player->addEffect($effect3);
						$effect10->setAmplifier($eff[2][1]);
						$effect10->setDuration(20*$eff[3][2]);
							$player->addEffect($effect10);
						$effect11->setAmplifier($eff[2][1]);
						$effect11->setDuration(20*$eff[3][2]);
							$player->addEffect($effect11);
						$player->sendTip($fire[7][3]);
							}
					break;
					
					case $fire[8][1]:
							if($fd->get($fire[8][1]) == $pname)
							{
						$effect12->setAmplifier($eff[2][1]);
						$effect12->setDuration(20*$eff[2][2]);
							$player->addEffect($effect12);
						$effect3->setAmplifier($eff[2][1]);
						$effect3->setDuration(20*$eff[2][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[2][1]);
						$effect11->setDuration(20*$eff[2][2]);
							$player->addEffect($effect11);
						$effect10->setAmplifier($eff[2][1]);
						$effect10->setDuration(20*$eff[2][2]);
							$player->addEffect($effect10);
						$player->sendTip($fire[8][3]);
							}
					break;
					
					case $fire[9][1]:
							if($fd->get($fire[9][1]) == $pname)
							{
						$effect12->setAmplifier($eff[2][1]);
						$effect12->setDuration(20*$eff[1][2]);
							$player->addEffect($effect12);
						$effect3->setAmplifier($eff[2][1]);
						$effect3->setDuration(20*$eff[1][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[2][1]);
						$effect11->setDuration(20*$eff[1][2]);
							$player->addEffect($effect11);
						$effect10->setAmplifier($eff[2][1]);
						$effect10->setDuration(20*$eff[1][2]);
							$player->addEffect($effect10);
						$player->sendTip($fire[9][3]);
							}
					break;
					
					case $fire[10][1]:
							if($fd->get($fire[10][1]) == $pname)
							{
						$effect12->setAmplifier($eff[2][1]);
						$effect12->setDuration(20*$eff[0][2]);
							$player->addEffect($effect12);
						$effect3->setAmplifier($eff[2][1]);
						$effect3->setDuration(20*$eff[0][2]);
							$player->addEffect($effect3);
						$effect11->setAmplifier($eff[2][1]);
						$effect11->setDuration(20*$eff[0][2]);
							$player->addEffect($effect11);
						$effect10->setAmplifier($eff[2][1]);
						$effect10->setDuration(20*$eff[0][2]);
							$player->addEffect($effect10);
						$player->sendTip($fire[10][3]);
							}
					break;

				}
		}
   		       }
 
 }
																						