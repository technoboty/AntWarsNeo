<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\block\VanillaBlocks;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use TechnoBoty\AntWarsNeo\MapManager\Map;
use TechnoBoty\AntWarsNeo\MapManager\MapManager;
use TechnoBoty\AntWarsNeo\TeamManager\TeamGroup;

class Arena{

    //Const
    public const MAX_PLAYERS = 16;

    public const WAIT_STAGE = 0;
    private const I_STAGE = 1;
    private const II_STAGE = 2;
    private const III_STAGE = 3;
    private const IV_STAGE = 4;

    /** @var Player[] $players  */
    private array $players = [];
    private int $stage;

    private bool $lockedFlag = FALSE;

    private Map $arena;

    private TeamGroup $group;

    public function __construct(){
        $this->arena = MapManager::getInstance()->addNewArena();
        $this->stage = self::WAIT_STAGE;
        $this->group = new TeamGroup();
    }
    public function join(Player $player) : void{
        if($this->lockedFlag){return;}
        if(!array_key_exists($player->getName(),$this->players)){
            $this->players[$player->getName()] = $player;
            $player->teleport(new Position(0,64,0,$this->arena->getWorld()));
            if(count($this->players) == self::MAX_PLAYERS){$this->lockedFlag = TRUE;}
            $this->equip($player);
            $this->onMessage(1,$player);
        }
    }
    public function quit(Player $player) : void{
        if(array_key_exists($player->getName(),$this->players)){
            unset($this->players[$player->getName()]);
        }
        $player->teleport(new Position(0,100,0,Server::getInstance()->getWorldManager()->getDefaultWorld()));
        if(count($this->players) == 0){
            $this->lockedFlag = TRUE;
            ArenaManager::getInstance()->unsetArena($this);
        }
        $this->onMessage(2,$player);
    }
    public function alreadyJoin() : bool{
        return !$this->lockedFlag;
    }
    public function getPlayers() : array{
        return $this->players;
    }
    public function getStage() : int{
        return $this->stage;
    }
    public function equip(Player $player) : void{
        $inv = $player->getInventory();
        $player->setGamemode(GameMode::ADVENTURE());
        $inv->clearAll();
        $select = VanillaBlocks::WOOL()->asItem()->setCustomName(TextFormat::BLUE."Выбрать команду")->setLore(["SummerWorld"]);
        $quit = VanillaBlocks::REDSTONE()->asItem()->setCustomName(TextFormat::RED."Вернуться в хаб")->setLore(["SummerWorld"]);
        $inv->setItem(2,$select);
        $inv->setItem(6,$quit);
    }
    private function onMessage(int $type,?Player $player) : void{
        switch($type){
            case 1:
                $count = count($this->players);
                $max = self::MAX_PLAYERS;
                foreach($this->players as $pl){
                    $pl->sendMessage(TextFormat::YELLOW."{$player->getName()} присоиденился ".TextFormat::DARK_PURPLE."[{$count} / $max]");
                }
                break;
            case 2:
                $count = count($this->players);
                $max = self::MAX_PLAYERS;
                foreach($this->players as $pl){
                    if($pl->isConnected()){
                        $pl->sendMessage(TextFormat::YELLOW . "{$player->getName()} вышел " . TextFormat::DARK_PURPLE . "[{$count} / $max]");
                    }
                }
                if($player->isConnected()){
                    $player->sendMessage(TextFormat::YELLOW . "{$player->getName()} вышел " . TextFormat::DARK_PURPLE . "[{$count} / $max]");
                }
                break;
            case 3:
                break;
        }
    }
    public function getTeamGroup() : TeamGroup{
        return $this->group;
    }
    public function __destruct(){
        $this->arena->deleteWorld();
    }
}