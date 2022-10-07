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

namespace lenlenlL6\oneblock\command;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\world\Position;
use pocketmine\world\WorldCreationOptions;
use pocketmine\math\Vector3;
use lenlenlL6\oneblock\Oneblock;
use lenlenlL6\oneblock\task\CreateAnIslandTask;
use jojoe77777\FormAPI\SimpleForm;
use jojoe77777\FormAPI\CustomForm;
use czechpmdevs\multiworld\generator\void\VoidGenerator;

final class OneblockCommand extends Command{
  
  public function __construct(){
    parent::__construct("oneblock", "Open Oneblock's preferences", null, ["oneblock", "ob"]);
    $this->setPermission("oneblock.command.oneblock");
  }
  
  public function execute(CommandSender $player, string $label, array $args) : void{
    if($player instanceof Player){
      if($this->testPermission($player, "oneblock.command.oneblock")){
        $this->mainForm($player);
      }
    }else{
      $player->sendMessage("Please use this command in game");
    }
  }
  
  private function mainForm(Player $player){
    $form = new SimpleForm(function(Player $player, ?int $data = null){
      if($data === null){
        return;
      }
      switch($data){
        case 0:
          $this->islandManage($player);
        break;
        
        case 1:
          $this->friendsManage($player);
        break;
        
        case 2:
          $this->tierRankings($player);
        break;
      }
    });
    $form->setTitle("§eMain options");
    $form->addButton("§aIsland manage");
    $form->addButton("§aManage friends");
    $form->addButton("§aTier rankings");
    $form->sendToPlayer($player);
  }
  
  private function islandManage(Player $player){
    $form = new SimpleForm(function(Player $player, ?int $data = null){
      if($data === null){
        return;
      }
      switch($data){
        case 0:
          if(!Oneblock::$is->exists(Oneblock::convertPlayerNameHasASpace($player))){
            $worldCreationOptions = new WorldCreationOptions();
            $worldCreationOptions->setGeneratorClass(VoidGenerator::class);
            $worldCreationOptions->setSeed(0);
            $worldCreationOptions->setSpawnPosition(new Vector3(256, 65, 256));
            Server::getInstance()->getWorldManager()->generateWorld("oneblock@@" . Oneblock::convertPlayerNameHasASpace($player), $worldCreationOptions);
            Oneblock::$is->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".respawnLocation", "256:65:256");
            Oneblock::$is->save();
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.WAITING_CREATE_ISLAND"));
            Oneblock::$plugin->getScheduler()->scheduleDelayedTask(new CreateAnIslandTask($player), 50);
          }else{
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.ALREADY_HAVE_ISLAND"));
          }
        break;
        
        case 1:
          if(Oneblock::$is->exists(Oneblock::convertPlayerNameHasASpace($player))){
            $position = explode(":", Oneblock::$is->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".respawnLocation"));
            Server::getInstance()->getWorldManager()->loadWorld("oneblock@@" . Oneblock::convertPlayerNameHasASpace($player));
            $player->teleport(new Position((int)$position[0], (int)$position[1], (int)$position[2], Server::getInstance()->getWorldManager()->getWorldByName("oneblock@@" . Oneblock::convertPlayerNameHasASpace($player))));
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.BACK_ISLAND"));
          }else{
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.NO_ISLAND"));
          }
        break;
        
        case 2:
          if(Oneblock::$is->exists(Oneblock::convertPlayerNameHasASpace($player))){
            if($player->getWorld()->getDisplayName() === "oneblock@@" . Oneblock::convertPlayerNameHasASpace($player)){
              Oneblock::$is->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".respawnLocation", $player->getLocation()->getX() . ":" . $player->getLocation()->getY() . ":" . $player->getLocation()->getZ());
              Oneblock::$is->save();
              $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.SET_NEW_RESPAWN_LOCATION"));
            }else{
              $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.WORLD_IS_WRONG"));
            }
          }else{
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.NO_ISLAND"));
          }
        break;
        
        case 3:
          if(Oneblock::$is->exists(Oneblock::convertPlayerNameHasASpace($player))){
            Server::getInstance()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), "mw delete oneblock@@" . Oneblock::convertPlayerNameHasASpace($player));
            Oneblock::$is->removeNested(Oneblock::convertPlayerNameHasASpace($player));
            Oneblock::$is->save();
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.DELETE_ISLAND"));
          }else{
            $player->sendMessage("§7[OneBlock] " . Oneblock::$config->getNested("lang.NO_ISLAND"));
          }
        break;
      }
    });
    $form->setTitle("§eIsland Manage");
    $form->addButton("§aCreate an island");
    $form->addButton("§aTeleport to your island");
    $form->addButton("§aSet respawn location");
    $form->addButton("§cDelete your island");
    $form->sendToPlayer($player);
  }
  
  private function friendsManage(Player $player){
    $form = new SimpleForm(function(Player $player, ?int $data = null){
      if($data === null){
        return;
      }
      switch($data){
        case 0:
          $this->invitations($player);
        break;
        
        case 1:
          $this->yourFriends($player);
        break;
        
        case 2:
          $this->sendInvitation($player);
        break;
        
        case 3:
          $this->deleteFriends($player);
        break;
      }
    });
    $form->setTitle("§eFriends Manage");
    $form->addButton("§aInvitations\n§7Number of invitations: §c" . count(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".invitations")));
    $form->addButton("§aYour friends");
    $form->addButton("§aSend invitation");
    $form->addButton("§aDelete friends");
    $form->sendToPlayer($player);
  }
  
  private function invitations(Player $player){
    $playerData = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".invitations");
    $form = new SimpleForm(function(Player $player, ?int $data = null) use($playerData){
      if($data === null){
        return;
      }
      $this->invitationOption($player, $playerData[$data]);
    });
    $form->setTitle("§eInvitations");
    $form->setContent(((count($playerData) > 0) ? "§aYour Invitations:" : "§cYou don't have any invitations"));
    foreach($playerData as $playerName){
      $form->addButton("§a" . $playerName . "\n§7Press to select");
    }
    $form->sendToPlayer($player);
  }
  
  private function invitationOption(Player $player, string $invitation){
    $form = new SimpleForm(function(Player $player, ?int $data = null) use($invitation){
      if($data === null){
        return;
      }
      $oldData = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".invitations");
      switch($data){
        case 0:
          $jjk = Oneblock::$player->getNested($invitation . ".invitations");
          if(count(array_keys($jjk, Oneblock::convertPlayerNameHasASpace($player))) > 0){
            array_splice($jjk, array_search(Oneblock::convertPlayerNameHasASpace($player), $jjk), 1);
            Oneblock::$player->setNested($invitation . ".invitations", $jjk);
            Oneblock::$player->save();
          }
          array_splice($oldData, array_search($invitation, $oldData), 1);
          Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".invitations", $oldData);
          $friends = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".friends");
          $friends[] = $invitation;
          Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".friends", $friends);
          $friends = Oneblock::$player->getNested($invitation . ".friends");
          $friends[] = Oneblock::convertPlayerNameHasASpace($player);
          Oneblock::$player->setNested($invitation . ".friends", $friends);
          Oneblock::$player->save();
          $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $invitation, Oneblock::$config->getNested("lang.ACCEPT_INVITATION")));
        break;
        
        case 1:
          array_splice($oldData, array_search($invitation, $oldData), 1);
          Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".invitations", $oldData);
          Oneblock::$player->save();
          $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $invitation, Oneblock::$config->getNested("lang.DECLINE_THE_INVITATION")));
        break;
      }
    });
    $form->setTitle("§e" . $invitation . "'s invitation");
    $form->addButton("§aAccept the invitation");
    $form->addButton("§cDecline the invitation");
    $form->sendToPlayer($player);
  }
  
  private function yourFriends(Player $player){
    $friends = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".friends");
    $form = new SimpleForm(function(Player $player, ?int $data = null) use($friends){
      if($data === null){
        return;
      }
      if(!Oneblock::$is->exists($friends[$data])){
        $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $friends[$data], Oneblock::$config->getNested("lang.FRIENDS_WITHOUT_ISLAND")));
        return;
      }
      $position = explode(":", Oneblock::$is->getNested($friends[$data] . ".respawnLocation"));
      Server::getInstance()->getWorldManager()->loadWorld("oneblock@@" . $friends[$data]);
      $player->teleport(new Position((int)$position[0], (int)$position[1], (int)$position[2], Server::getInstance()->getWorldManager()->getWorldByName("oneblock@@" . $friends[$data])));
      $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $friends[$data], Oneblock::$config->getNested("lang.TELEPORT_TO_FRIEND_ISLAND")));
    });
    $form->setTitle("§eYour friends");
    $form->setContent(((count($friends) > 0) ? "§aYour friends:" : "§cYou have no friends"));
    foreach($friends as $friend){
      $form->addButton("§a" . $friend . "\n§7Tap to teleport");
    }
    $form->sendToPlayer($player);
  }
  
  private function sendInvitation(Player $player){
    $players = [];
    foreach(Server::getInstance()->getOnlinePlayers() as $playerNjk){
      if($playerNjk->getName() !== $player->getName()){
        $players[] = $playerNjk->getName();
      }
    }
    $form = new CustomForm(function(Player $player, ?array $data = null) use($players){
      if($data === null){
        return;
      }
      if(Server::getInstance()->getPlayerByPrefix($players[$data[0]]) === null){
        $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $players[$data[0]], Oneblock::$config->getNested("lang.PLAYER_DOES_NOT_EXIST")));
        return;
      }
      if(count(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace(Server::getInstance()->getPlayerByPrefix($players[$data[0]])) . ".invitations")) >= 10){
        $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $players[$data[0]], Oneblock::$config->getNested("lang.TOO_MUCH_INVITATIONS")));
        return;
      }
      if(count(array_keys(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace(Server::getInstance()->getPlayerByPrefix($players[$data[0]])) . ".invitations"), Oneblock::convertPlayerNameHasASpace($player))) > 0){
        $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $players[$data[0]], Oneblock::$config->getNested("lang.THE_INVITATION_EXISTS")));
        return;
      }
      if(count(array_keys(Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace(Server::getInstance()->getPlayerByPrefix($players[$data[0]])) . ".friends"), Oneblock::convertPlayerNameHasASpace($player))) > 0){
        $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $players[$data[0]], Oneblock::$config->getNested("lang.FRIEND_ALREADY_EXIST")));
        return;
      }
      $invitations = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace(Server::getInstance()->getPlayerByPrefix($players[$data[0]])) . ".invitations");
      $invitations[] = Oneblock::convertPlayerNameHasASpace($player);
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace(Server::getInstance()->getPlayerByPrefix($players[$data[0]])) . ".invitations", $invitations);
      Oneblock::$player->save();
      $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $players[$data[0]], Oneblock::$config->getNested("lang.COMPLETED_SEND_INVITATIONS")));
    });
    $form->setTitle("§eSend invitation");
    $form->addDropdown("§aSelect a player:", $players);
    $form->sendToPlayer($player);
  }
  
  private function deleteFriends(Player $player){
    $friends = Oneblock::$player->getNested(Oneblock::convertPlayerNameHasASpace($player) . ".friends");
    $form = new CustomForm(function(Player $player, ?array $data = null) use($friends){
      if($data === null){
        return;
      }
      $player->sendMessage("§7[OneBlock] " . str_replace("{name}", $friends[$data[0]], Oneblock::$config->getNested("lang.DELETE_FRIENDS")));
      $frs = Oneblock::$player->getNested($friends[$data[0]] . ".friends");
      array_splice($frs, array_search(Oneblock::convertPlayerNameHasASpace($player), $frs), 1);
      Oneblock::$player->setNested($friends[$data[0]] . ".friends", $frs);
      array_splice($friends, array_search($friends[$data[0]], $friends), 1);
      Oneblock::$player->setNested(Oneblock::convertPlayerNameHasASpace($player) . ".friends", $friends);
      Oneblock::$player->save();
    });
    $form->setTitle("§eDelete friends");
    $form->addDropdown("§aSelect a friend:", $friends);
    $form->sendToPlayer($player);
  }
  
  private function tierRankings(Player $player, int $page = 1){
    $maxPage = ceil(count(Oneblock::$player->getAll()) / 10);
    $form = new CustomForm(function(Player $player, ?array $data = null) use($maxPage){
      if($data === null){
        return;
      }
      if(!is_numeric($data[1]) or (int)$data[1] > $maxPage or (int)$data[1] <= 0){
        $data[1] = $maxPage;
      }
      $this->tierRankings($player, $maxPage);
    });
    $data = [];
    $str = "";
    foreach(Oneblock::$player->getAll() as $name => $dataL){
      $data[$name] = $dataL["tier"];
    }
    arsort($data);
    $data = array_slice($data, $page*10 - 10, 10);
    $index = $page*10 - 10 + 1;
    foreach($data as $name => $tier){
      $str .= "§e" . $index . " §a" . $name . " " . $tier . "\n";
    }
    $form->setTitle("§eTier Rankings §7" . $page . "§b/§c" . $maxPage);
    $form->addLabel($str);
    $form->addInput("§aEnter the page number:", "Ex: 1");
    $form->sendToPlayer($player);
  }
}
