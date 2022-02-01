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
use pocketmine\block\VanillaBlocks;

class Tier8Task extends Task{

  /** @var Oneblock $tier */
  public Oneblock $main;

  public $block;
  
  public function __construct(Oneblock $main, $block){
    $this->main = $main;
    $this->block = $block;
  }
  
  public function onRun() : void{
    $world = $this->block->getPosition()->getWorld();
    switch(mt_rand(1, 28)){
      case 1:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());
        break;
        
        case 3:
          $world->setBlock($this->block->getPosition(), VanillaBlocks::IRON_ORE());
          break;
          
          case 7:
            $world->setBlock($this->block->getPosition(), VanillaBlocks::COBBLESTONE());
            break;
            
            case 8:
              $world->setBlock($this->block->getPosition(), VanillaBlocks::GOLD_ORE());
              break;
              
              case 9:
                $world->setBlock($this->block->getPosition(), VanillaBlocks::LAPIS_LAZULI_ORE());
                break;
                
                case 13:
                  $world->setBlock($this->block->getPosition(), VanillaBlocks::COAL_ORE());
                  break;
                  
                  case 14:
                    $world->setBlock($this->block->getPosition(), VanillaBlocks::EMERALD_ORE());
                    break;
                    
                    case 19:
                      $world->setBlock($this->block->getPosition(), VanillaBlocks::REDSTONE_ORE());
                      break;
                      
                      case 16:
                        $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());
                        break;
                        
                        case 23:
                       $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());   
                          break;
                          
                          case 25:
                            $world->setBlock($this->block->getPosition(), VanillaBlocks::EMERALD_ORE());
                            break;
                            
                            case 28:
                              $world->setBlock($this->block->getPosition(), VanillaBlocks::DIAMOND_ORE());
                              break;
          
        default:
        $world->setBlock($this->block->getPosition(), VanillaBlocks::STONE());
        break;
    }
  }
}     