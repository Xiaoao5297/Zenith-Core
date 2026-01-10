<?php

/*
 * This file is a part of WalkingParticles.
 * Copyright (C) 2015 CyberCube-HK
 *
 * WalkingParticles is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WalkingParticles is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WalkingParticles. If not, see <http://www.gnu.org/licenses/>.
 */
namespace WalkingParticles;

use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\level\particle\BubbleParticle;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\DustParticle;
use pocketmine\level\particle\EnchantParticle;
use pocketmine\level\particle\InstantEnchantParticle;
use pocketmine\level\particle\ExplodeParticle;
use pocketmine\level\particle\LargeExplodeParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\EntityFlameParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\InkParticle;
use pocketmine\level\particle\ItemBreakParticle;
use pocketmine\level\particle\LavaDripParticle;
use pocketmine\level\particle\LavaParticle;
use pocketmine\level\particle\Particle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\RedstoneParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\level\particle\SplashParticle;
use pocketmine\level\particle\SporeParticle;
use pocketmine\level\particle\TerrainParticle;
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\level\particle\WaterParticle;
use pocketmine\level\particle\EnchantmentTableParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\level\particle\AngryVillagerParticle;
use pocketmine\level\particle\RainSplashParticle;
use pocketmine\level\particle\DestroyBlockParticle;
use WalkingParticles\WalkingParticles;

class Particles{

	public $plugin;

	public function __construct(WalkingParticles $plugin){
		$this->plugin = $plugin;
	}

	public function getTheParticle($name, Vector3 $pos, $data = null){
		switch($name):
			case "explode":
				return new ExplodeParticle($pos);
			case "largeexplode":
				return new LargeExplodeParticle($pos);
			case "hugeexplode":
				return new HugeExplodeParticle($pos);
			case "bubble":
				return new BubbleParticle($pos);
			case "splash":
				return new SplashParticle($pos);
			case "water":
				return new WaterParticle($pos);
			case "crit":
			case "critical":
				return new CriticalParticle($pos);
			case "spell":
				return new EnchantParticle($pos);
			case "instantspell":
				return new InstantEnchantParticle($pos);
			case "smoke":
				return new SmokeParticle($pos, ($data === null ? 0 : $data));
			case "dripwater":
				return new WaterDripParticle($pos);
			case "driplava":
				return new LavaDripParticle($pos);
			case "townaura":
			case "spore":
				return new SporeParticle($pos);
			case "portal":
				return new PortalParticle($pos);
			case "entityflame":
				return new EntityFlameParticle($pos);
			case "flame":
				return new FlameParticle($pos);
			case "lava":
				return new LavaParticle($pos);
			case "reddust":
			case "redstone":
				return new RedstoneParticle($pos, ($data === null ? 1 : $data));
			case "snowballpoof":
			case "snowball":
				return new ItemBreakParticle($pos, Item::get(Item::SNOWBALL));
			case "slime":
				return new ItemBreakParticle($pos, Item::get(Item::SLIMEBALL));
			case "heart":
				return new HeartParticle($pos, ($data === null ? 0 : $data));
			case "ink":
				return new InkParticle($pos, ($data === null ? 0 : $data));
			case "enchantmenttable":
			case "enchantment":
				return new EnchantmentTableParticle($pos);
			case "happyvillager":
				return new HappyVillagerParticle($pos);
			case "angryvillager":
				return new AngryVillagerParticle($pos);
			case "droplet":
			case "rain":
				return new RainSplashParticle($pos);
			case "colorful":
			case "colourful":
				return new TerrainParticle($pos, Block::get(round(rand(0, 114))));
		endswitch
		;
		if(substr($name, 0, 5) == "item_"){
			$arr = explode("_", $name);
			return new ItemBreakParticle($pos, new Item((int) $arr[1]));
		}
		if(substr($name, 0, 6) == "block_"){
			$arr = explode("_", $name);
			return new TerrainParticle($pos, Block::get((int) $arr[1]));
		}
		if(substr($name, 0, 9) == "desblock_"){
			$arr = explode("_", $name);
			return new DestroyBlockParticle($pos, Block::get((int) $arr[1]));
		}
		return "unknown_particle";
	}

	public function getRandomParticle(){
		// I think the TerrainParticle performs quite well
		$random = round(rand(0, 114));
		return "block_" . $random;
	}

	public function getAll(){
		// For string output
		return [
				"bubble",
				"explode",
				"splash",
				"water",
				"critical",
				"spell",
				"smoke",
				"driplava",
				"dripwater",
				"spore",
				"portal",
				"flame",
				"entityflame",
				"lava",
				"reddust",
				"snowball",
				"heart",
				"ink",
				"hugeexplode",
				"largeexplode",
				"instantspell",
				"slime",
				"enchantment",
				"happyvillager",
				"angryvillager",
				"droplet",
				"colorful",
				"item_{id}",
				"block_{id}",
				"desblock_{id}"
		];
	}

}
?>