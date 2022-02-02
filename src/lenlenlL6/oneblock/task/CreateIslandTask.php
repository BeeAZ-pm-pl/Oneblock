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
 
 
 Tick 5s when player create an island.
 Fixed error 2 blocks in pm3.
*/

namespace lenlenlL6\oneblock\task;

use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\scheduler\Task;
use lenlenlL6\oneblock\Oneblock;

class CreateIslandTask extends Task{

  /** @var Oneblock $tier */
  public Oneblock $main;

  /** @var Player $player */
  public Player $player;

  public function __construct(Oneblock $main, Player $player){
    $this->main = $main;
    $this->player = $player;
  }
  
  public function onRun() : void{
    $this->main->getServer()->getWorldManager()->loadWorld("oneblock-" . $this->player->getName());
    $world = $this->main->getServer()->getWorldManager()->getWorldByName("oneblock-" . $this->player->getName());
    $world->setBlock(new Position(256, 65, 256, $world), VanillaBlocks::CHEST());
    $tile = $world->getTile(new Position(256, 65, 256, $world));
    foreach($this->main->getConfig()->get("items") as $items){
      $ex = explode(".", $items);
      $tile->getInventory()->addItem(ItemFactory::getInstance()->get($ex[0], $ex[1], $ex[2]));
    }
    $msg = $this->main->lang->get("CREATE_ISLAND_COMPLETE");
    $this->player->sendMessage($this->main->prefix . " $msg");
  }
}
