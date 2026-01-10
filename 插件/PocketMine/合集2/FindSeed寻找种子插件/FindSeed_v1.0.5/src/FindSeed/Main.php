<?php
namespace FindSeed;
use pocketmine\plugin\Plugin;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\block\Grass;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\inventory\Inventory;

class Main extends PluginBase implements Listener{
    public function onEnable()
	{
		$this->getLogger()->info("§c成功载入§a[RealSurvive系列]§b寻找种子&摘苹果插件");
		$this->getLogger()->info("§cRS系列是由KingLegend开发出的一套特色生存系统，让玩家体验到最真实的“生存模式”");
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
//创建Config对象
		@mkdir($this->getDataFolder(),0777,true);
		$this->config=new Config($this->getDataFolder()."config.yml",Config::YAML,array());
		$this->config->set("获取小麦种子的概率:",30);
		$this->config->set("获取胡萝卜的概率:",20);
		$this->config->set("获取马铃薯的概率",10);
		$this->config->set("获得苹果的概率:",25);
		$this->config->save();
		}
		  public function onPlayerJoinEvent
(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $player->sendMessage("§8本服务器设有仿真插件:§7寻找种子&摘苹果");
    }
		  public function onBlockBreakEvent(BlockBreakEvent $event)
		  {
		      $player = $event->getPlayer();
		      $item = $event->getItem();
		      $block = $event->getBlock()->getId();
		      $m = mt_rand(0,100);
		      $inv = $player->getInventory();
		      if($item = 269 || $item = 273 || $item = 277 || $item = 284)
		      {
		          switch($block)
		          {
		              case 2:
		                  if($m <= $this->config->get("获取小麦种子的概率:"))
		                  {
		                      $inv->addItem(new Item(295, 0, 1));
		                      $player->sendMessage("§a[获得成就]§c“好眼力”§a你在草地里找出了小麦种子。");
		                  }
		                  break;
		              case 12:
		                  if($m <= $this->config->get("获取胡萝卜的概率:"))
		                  {
		                      $inv->addItem(new Item(391, 0, 1));
		                      $player->sendMessage("§a[获得成就]§c“真稀奇”§a你在沙子里挖到了胡萝卜！");
		                  }
		                  break;
		              case 3:
		                  if($m <= $this->config->get("获取马铃薯的概率:"))
		                  {
		                      $inv->addItem(new Item(393, 0, 1));
		                      $player->sendMessage("§a[获得成就]§c“好身手”§a你满身泥土和臭汗，终于挖出了土豆！");
		                  }
		                  break;
		          }
		      }
		      if($block == 18)
		      {
		          if($m <= $this->config->get("获取苹果的概率:"))
		                  {
		                      $inv->addItem(new Item(260, 0, 1));
		                      $player->sendMessage("§a[获得成就]§c“我爱打树叶”§a身子虽然矮，但是睿智的你还是打下来苹果了！");
		                  }
		      }
		  }
}