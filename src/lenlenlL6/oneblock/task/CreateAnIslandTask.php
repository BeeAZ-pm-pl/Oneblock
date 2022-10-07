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

use pocketmine\Server;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use lenlenlL6\oneblock\Oneblock;

final class CreateAnIslandTask extends Task{
  
  private Player $player;
  
  public function __construct(Player $player, ){
    $this->player = $player;
  }
  
  public function onRun() : void{
    $world = Server::getInstance()->getWorldManager()->getWorldByName("oneblock@@" . Oneblock::convertPlayerNameHasASpace($this->player));
    $world->setBlock(new Vector3(256, 63, 256), VanillaBlocks::BEDROCK());
    $this->player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.CREATE_ISLAND_COMPLETE"));
  }
}
