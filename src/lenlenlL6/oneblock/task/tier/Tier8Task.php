<?php
/*
___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
 
 ((/)) An upgrade of oneblock pm3 made by lenlenlL6 and Dora.
 ((/)) If you have problems with the plugin, contact me.
 ---> Facebook: https://www.facebook.com/profile.php?id=100071316150096
 ---> Github: https://github.com/lenlenlL6
 ((/)) Copyright by lenlenlL6 and Dora.
*/

namespace lenlenlL6\oneblock\task\tier;

use lenlenlL6\oneblock\Oneblock;
use pocketmine\scheduler\Task;
use pocketmine\block\BlockFactory;

class Tier8Task extends Task {

	/** @var Oneblock $tier */
	public Oneblock $main;

	public $block;

	public function __construct(Oneblock $main, $block) {
		$this->main = $main;
		$this->block = $block;
	}

	public function onRun(): void {
		$world = $this->block->getPosition()->getWorld();
		$array = [];
		foreach ($this->main->getConfig()->get("8") as $blocks) {
			$ex = explode(".", $blocks);
			$array[] = BlockFactory::getInstance()->get($ex[0], $ex[1]);
		}
		$rand = array_rand($array);
		$world->setBlock($this->block->getPosition(), $array[$rand]);
	}
}
