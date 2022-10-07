<?php
/*
___             _     _            _    
  / _ \ _ __   ___| |__ | | ___   ___| | __
 | | | | '_ \ / _ \ '_ \| |/ _ \ / __| |/ /
 | |_| | | | |  __/ |_) | | (_) | (__|   < 
  \___/|_| |_|\___|_.__/|_|\___/ \___|_|\_\
 
 ((/)) An upgrade of oneblock pm3 made by lenlenlL6, BeeAZ-pm-pl.
 ((/)) If you have problems with the plugin, contact me.
 ---> Facebook: https://www.facebook.com/profile.php?id=100071316150096
 ---> Github: https://github.com/lenlenlL6
 ((/)) Copyright by lenlenlL6, BeeAZ-pm-pl.
*/

namespace lenlenlL6\oneblock\task;

use pocketmine\scheduler\Task;
use pocketmine\world\Position;
use pocketmine\block\Block;

final class SetBlockTask extends Task{
  
  private Position $position;
  
  private Block $block;
  
  public function __construct(Position $position, Block $block){
    $this->position = $position;
    $this->block = $block;
  }
  
  public function onRun() : void{
    $this->position->getWorld()->setBlock($this->position, $this->block);
  }
}
