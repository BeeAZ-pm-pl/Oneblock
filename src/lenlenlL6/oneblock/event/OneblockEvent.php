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
 
 
 Oneblock Event
*/

namespace lenlenlL6\oneblock\event;

use pocketmine\player\Player;
use pocketmine\event\plugin\PluginEvent;
use lenlenlL6\oneblock\Oneblock;

class OneblockEvent extends PluginEvent {

	/** @var Oneblock $tier */
	public Oneblock $main;

	public function __construct(Oneblock $main) {
		$this->main = $main;
	}
}
