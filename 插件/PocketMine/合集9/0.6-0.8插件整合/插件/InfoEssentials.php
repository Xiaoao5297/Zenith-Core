<?php
/*
__PocketMine Plugin__
class=InfoEssentials
apiversion=10,11,12
version=Gamma 3.14159265.271
name=InfoEssentials
author=PEMapModder
*/
/*
Gamma update summary:
3			=> initial commit of gamma
3.1			=> added /setarmor
3.1.4		=> optimized the evaluation and interpreting of item strings
===RELEASE ON POCKETMINE FORUMS===
3.141						=> added permissions config file
3.1415						=> corrected the misspelled PermissionPlus (no 's')
3.1415.1					=> fixed crashing if PermissionPlus is installed
3.14159						=> added /rmarmor
3.141592					=> colourful update and op list
3.1415926					=> more colours as of TextFormat.php
3.14159265					=> login times record and inventory count								7/2/2014
3.14159265.27				=> number_format /getpos												7/2/2014
3.14159265.271				=> dismiss API message of Henteko Minecart (with test of course)		7/2/2014
*/
class InfoEssentials implements Plugin{
	private $c,$s;
	//private $langEn;
	public function __construct(ServerAPI $a,$s=0){}
	public function __destruct(){}
	public function init(){
		$this->s=ServerAPI::request();
		$s=$this->s;
		@mkdir($s->api->plugin->configPath($this)."database/");
		$api=$this->s->api;
		$b=ServerAPI::request()->api->ban;
		$c=ServerAPI::request()->api->console;
		$this->c=$c;
		$c->register("getping","[IGN] get ping of a player",array($this,"getPing"));
		$c->register("seearmor","[IGN] see if a player is sleeping",array($this,"seeArmor"));
		$c->register("seegm","[IGN] see the gamemode of a player",array($this,"seeGm"));
		$c->register("getpos","[IGN] get the position of a player",array($this,"getPos"));
		$c->register("pw","ping warn",array($this,"pingWarn"));
		$c->register("setarmor","<material> <part> [IGN] set a player's armor",array($this,"setArmor"));
		$c->register("rmarmor","<part> [IGN] set part of a player's armor to empty",array($this, "rmArmor"));
		$c->register("seeop","see op list",array($this,"seeOp"));
		$c->register("sessions","[IGN] see the number of logins of a player", array($this,"seeLogout"));
		$c->register("invcount","<count> [IGN] see the amount of the item in a player's inventory", array($this,"invCnt"));
		/*$ext="yml";
		if(file_exists(FILE_PATH."plugins/InfoEssentials_items_language_file.txt"))$ext="txt";
		$this->langEn=new Config(FILE_PATH."plugins/InfoEssentials_items_language_file.$ext", CONFIG_YAML, array(
			"diamonds"=>264
		));
		*/
		$this->s->addHandler("player.spawn",array($this,"entered"));
		$this->s->addHandler("player.quit",array($this,"onPlayerQuit"));
		$path=$api->plugin->configPath($this)."settings.";
		foreach($api->plugin->getList() as $plugin)
			if($plugin["name"]=="PermissionPlus")$ppLoaded=true;
		if(!isset($ppLoaded)){
			if(file_exists($path."txt"))$path.="txt";
			else $path.="yml";
			$settings=new Config($path,CONFIG_YAML,array(
				"non-op permissions"=>array(
					"README"=>"You can also allow non-ops to use commands of other plugins / default commands here",
					"/getping"=>true,
					"/seearmor"=>true,
					"/seegm"=>true,
					"/getpos"=>true,
					"/pw"=>false,//just to stop annoying norm players
					"/setarmor"=>false,
					"/rmarmor"=>false,
					"/seeop"=>true,
					"/sessions"=>true
				),
				/*"alias"=>array(
					"/hist"=>"/sessions",
					"/logins"=>"/sessions",
					"/pos"=>"/getpos",
					"/seepos"=>"/getpos",
					"/seearm"=>"/seearmor",
					"/invcnt"=>"/invcount"
				)*/
			));
			if(!($settings instanceof Config))return;
			foreach($settings->get("non-op permissions") as $cmd=>$boolean){
				if($cmd=="README")continue;
				if($boolean===true)
					$b->cmdWhitelist(substr($cmd,1));
			}
			/*foreach($settings->get("alias") as $orig=>$alias)
				$c->alias(substr($alias,1), substr($orig,1));//str_replace("/", "", "$cmd");
			*/
		}
	}
	public function pingWarn($c,$a,$p){//to be used
		return;
		foreach($this->s->api->player->getAll() as $p){
			if($p->getLag()>1000){
				$p->sendChat("Your ping is too high!");
				$p->sendChat("Having a ping of {$p->getLag()} ms is a poor connection!");
			}
		}
	}
	public function entered($d,$e){
		console("$d entered");
		$this->c->run("pw");
	}
	public function onPlayerQuit(Player $d,$e){
		$file=$this->s->api->plugin->configPath($this)."database/$d.txt";
		$logouts=0;
		if(file_exists($file))
			$logouts=(int)file_get_contents($file);
		$logouts++;
		file_put_contents($file, "$logouts");
	}
	public function seeLogout($c,$a,$p){
		$output="";
		$this->c->run("pw");
		if(!isset($a[0])){
			if(!($p instanceof Player))
				return "Usage: /$c [IGN]";
			$target=$p->username;
		}
		else{
			$target=$a[0];
		}
		$file=$this->s->api->plugin->configPath($this)."database/$target.txt";
		$logouts=file_get_contents($file);
		if($logouts===false){
			if($this->s->api->player->get($target, false) instanceof Player)
				return "$target is the first time being on this server. At least, the 1st time after InfoEssentials is installed on this server.";
			$output.="Player $target not found.\n";
			$newPlayer=$this->s->api->player->get($target, true);
			if($newPlayer instanceof Player){
				$ign=$newPlayer->username;
				$file=$this->s->api->plugin->configPath($this)."database/$ign.txt";
				$logouts=file_get_contents($file);
				$target=$ign;
				if($logouts===false)return "$target is the 1st time being on this server. At least, the 1st time after InfoEssentials is installed on this server.";
			}
			else return $output."Nor are any players of alike names found.";
		}
		$logins=(int)$logouts+1;
		$suf="th";
		$mod=$logins%100;
		if($mod!=11 and $mod!=12 and $mod!=13){//grammar. Sometimes I wonder who made this troublesome rule of 11,12,13.
			$mod%=10;
			if($mod==1)$suf="st";
			elseif($mod==2)$suf="nd";
			elseif($mod==3)$suf="rd";
		}
		if($this->s->api->player->get($target, false)===false)
			return "$target has been on this server for $logouts times.".($logins<10?" At least, the $logins"."$suf time after InfoEssentials is installed on this server.":"");
		return "$target is the $logins"."$suf time being on this server.".($logins<10?" At least, the $logins"."$suf time after InfoEssentials is installed on this server.":"");
	}
	public function getPing($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[0])){
			if(!($p instanceof Player))
				return "Usage: /getping [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[0]);
		if($p===false)return "Player not found";
		return "{$p}'s ping: ".FORMAT_AQUA.$p->getLag().FORMAT_RESET." pakcet loss: {$p->getPacketLoss()} bandwidth: {$p->getBandwidth()}\n";
	}
	public function seeArmor($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[0])){
			if(!($p instanceof Player))
				return "Usage: /seearmor [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[0]);
		if($p===false)return "Player not found";
		return "\n $p's armor: \n".
			"Helmet:     ".$this->evalArmor($p->getArmor(0)->getID(),$p)."\n".
			"Chestplate: ".$this->evalArmor($p->getArmor(1)->getID(),$p)."\n".
			"Leggings:   ".$this->evalArmor($p->getArmor(2)->getID(),$p)."\n".
			"Boots:      ".$this->evalArmor($p->getArmor(3)->getID(),$p);
	}
	public function seeGm($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[0])){
			if(!($p instanceof Player))
				return "Usage: /seegm [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[0]);
		if($p===false)return "Player not found";
		return "$p is in ".($p->getGamemode()=="creative"?FORMAT_YELLOW:FORMAT_AQUA)."{$p->getGamemode()} mode.\n";
	}
	public function getPos($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[0])){
			if(!($p instanceof Player))
				return "Usage: /getpos [IGN]";
			$i=$p;
		}
		else $i=ServerAPI::request()->api->player->get($a[0]);
		if($i===false)return "Player not found";
		$p=$i->entity;
		return "$i is in world \"".$p->level->getName()."\" at (X,Y,Z) (".FORMAT_RED.number_format($p->x,2).",".FORMAT_YELLOW.number_format($p->y,2).",".FORMAT_AQUA.number_format($p->z, 2).").".FORMAT_RESET;
	}
	public function setArmor($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[2])){
			if(!($p instanceof Player))
				return "Usage: /setarmor <material> <part> [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[2]);
		if($p===false)return "Player not found";
		if($this->interpretMaterial($a[0])===false or $this->interpretPart($a[1])===false)
			return "Usage: /setarmor <material> <part> [IGN]";
		$p->setArmor($this->interpretPart($a[1]), BlockAPI::getItem($this->interpretMaterial($a[0])+$this->interpretPart($a[1])));
		return "$p's armor is set\n";
	}
	public function rmArmor($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[1])){
			if(!($p instanceof Player))
				return "Usage: /rmarmor <part> [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[1]);
		if($p===false)return "Player not found";
		if($this->interpretPart($a[0])===false)
			return "Usage: /rmarmor <part> [IGN]";
		$p->setArmor($this->interpretPart($a[0]), BlockAPI::getItem(0));
		return "Removed $p's part of armor\n";
	}
	public function seeOp($c,$a){
		$list=explode("\n",file_get_contents(FILE_PATH."ops.txt"));
		if(!isset($a[0]))$a[0]=1;
		if(!is_numeric($a[0]))return "Usage: /seeop [page]";
		else $n=((int)$a[0])-1;
		$out="Op list: Page ".($n+1)." of ".(ceil(count($list)/5))."\n";
		for($i=0;$i<5;$i++){
			$out.=($list[$n*5+$i]."\n");
		}
		return $out;
	}
	public function invCnt($c,$a,$p){
		$this->c->run("pw");
		if(!isset($a[1])){
			if(!($p instanceof Player))
				return "Usage: /$c <id> [IGN]";
		}
		else $p=ServerAPI::request()->api->player->get($a[1]);
		if($p===false)return "Player not found";
		if($p->getGamemode()%2===1)
			return "Hey, $p is in ".$p->getGamemode()." mode. Ask Mojang MCPE devs if you want to know if ".$a[0]." is in a creative inventory.";
		return "There are ".FORMAT_GREEN.$this->countInventory($p, $a[0]).FORMAT_RESET." of item ".$a[0]." in $p's inventory.";
	}
	public function countInventory($player, $needle){//copied from my another plugin, PRM
		$haystack=$player->inventory;
		$result=0;
		foreach($haystack as $item){
			if($item->getID() === $needle)
				$result+=$item->count;
		}
		return $result;
	}
	protected function interpretMaterial($str){
		switch(strtolower($str)){
			case "diamond":return 310;
			case "gold":case "budder":case "butter":case "golden":return 314; 
			case "leather":return 298;
			case "iron":return 306;
			case "chain":return 302;
			case "0":case "null":case "air":case "empty":case "off":case "nothing":return 0;
			default:return false;
		}
	}
	protected function interpretPart($str){
		$str=strtolower(str_replace("s","",$str));
		switch($str){
			case "1":case "helm":case "helmet":
				return 0;
			case "2":case "tunic":case "chet":case "chetplate":case "cp":case "hirt":
				return 1;
			case "3":case "legging":case "pant":case "trouer":
				return 2;
			case "4":case "boot":case "shoe":case "sneaker":
				return 3;
			default:
				return false;
		}
	}
	protected function evalArmor($id,$p="Unknown"){
		if($id==0)return FORMAT_LIGHT_PURPLE."Empty".FORMAT_RESET;
		switch(round($id/4)){
			default:$o="unknown-material";console(FORMAT_YELLOW."[WARNING] [InfoEss] Something strange happened. $p has an armor id of $id");break;
			case 75:$o=FORMAT_RED."leather".FORMAT_RESET;break;
			case 76:$o=FORMAT_GRAY."chain".FORMAT_RESET;break;
			case 77:$o=FORMAT_LIGHT_PURPLE."iron".FORMAT_RESET;break;
			case 78:$o=FORMAT_AQUA."diamond".FORMAT_RESET;break;
			case 79:$o=FORMAT_GOLD."golden".FORMAT_RESET;break;
		}
		switch($id%4){
			case 2:return "$o helmet";
			case 3:return "$o chestplate";
			case 0:return "$o leggings";
			case 1:return "$o boots";
		}
		console(FORMAT_RED."[ERROR] [InfoEss] InfoEssentials has an unexpected behaviour of itself. Please send this report to @PEMapModder on PocketMine Forums:\n".
			date("d-n-y G:i:s")." $id is attempted to be evaluated for armor type. Data: ".((isset($p->entity) and ($p instanceof Player))?($p->getArmor(0)."\n".$p->getArmor(1)."\n".$p->getArmor(2)."\n".$p->getArmor(3)):"Unknown"));
	}
}
