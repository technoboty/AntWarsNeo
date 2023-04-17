<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\block\VanillaBlocks;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\MapManager\Map;
use TechnoBoty\AntWarsNeo\MapManager\MapManager;
use TechnoBoty\AntWarsNeo\Scoreboard\InGameScoreboards;
use TechnoBoty\AntWarsNeo\TeamManager\TeamGroup;

class Arena{

    //Const
    public const MAX_PLAYERS = 16;

    public const WAIT_STAGE = 0;
    private const START_STAGE = 1;
    private const I_STAGE = 2;
    private const II_STAGE = 3;
    private const III_STAGE = 4;

    private const IV_STAGE = 4;

    //===
    private int $startClock = 60;


    /** @var Player[] $players  */
    private array $players = [];
    private int $stage;

    private bool $lockedFlag = FALSE;

    private Map $arena;

    private TeamGroup $group;

    private TaskHandler $sidebar;
    private TaskHandler $clockDecrease;

    public function __construct(){
        $this->arena = MapManager::getInstance()->addNewArena();
        $this->stage = self::WAIT_STAGE;
        $this->group = new TeamGroup();
        $this->sidebar = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new class($this->arena->getWorld()->getFolderName()) extends Task{

            public function __construct(private string $arena){}

            public function onRun(): void{
                $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arena);
                if($arena != NULL){
                    $arena->sendSideBar();
                } else {
                    $this->getHandler()->cancel();
                }
            }
        },20);
    }
    public function join(Player $player) : void{
        if($this->lockedFlag){return;}
        if(!array_key_exists($player->getName(),$this->players)){
            $this->players[$player->getName()] = $player;
            $player->teleport(new Position(0,64,0,$this->arena->getWorld()));
            if(count($this->players) == self::MAX_PLAYERS){$this->lockedFlag = TRUE;}
            $this->equip($player);
            $this->onMessage(1,$player);
            $lobby = TextFormat::YELLOW.$this->arena->getWorld()->getFolderName();
            $count = count($this->players);
            $max = self::MAX_PLAYERS;
            $status = TextFormat::DARK_PURPLE."Ожидание игры ";
            $donate = TextFormat::GOLD."summer-world.me ";
            $lines = [
                1 => "Лобби: $lobby ",
                2 => "Статус: $status ",
                3 => "Игроков:".TextFormat::RED." $count / $max ",
                4 => "До старта: $this->startClock сек.",
                5 => "Донат: $donate "
            ];
            InGameScoreboards::getInstance()->setInLobbyScoreBoard($this->players,$lines);
            if(count($this->players) >= 4){
                if($this->stage != self::WAIT_STAGE) return;
                $this->stage = self::START_STAGE;
                $this->clockDecrease = Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new class($this->arena->getWorld()->getFolderName()) extends Task{

                    public function __construct(private string $arenaName){}
                    public function onRun(): void {
                        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
                        if($arena != NULL){
                            //TODO
                        } else {
                            $this->getHandler()->cancel();
                        }
                    }
                },20,20);
            }
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
        if($player->isConnected()){
            if($this->stage == self::WAIT_STAGE) {
                $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inlobby"));
            }
            Main::getInstance()->sendDefaultScoreBoard($player);
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
    public function getMap() : Map{
        return $this->arena;
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
    public function sendSideBar() : void{
        if($this->stage == self::WAIT_STAGE){
            $lobby = TextFormat::YELLOW.$this->arena->getWorld()->getFolderName();
            $count = count($this->players);
            $max = self::MAX_PLAYERS;
            $status = TextFormat::DARK_PURPLE."Ожидание игры ";
            $donate = TextFormat::GOLD."summer-world.me ";
            $lines = [
                1 => "Лобби: $lobby ",
                2 => "Статус: $status ",
                3 => "Игроков:".TextFormat::RED." $count / $max ",
                4 => "До старта: $this->startClock сек.",
                5 => "Донат: $donate "
            ];
            foreach($this->players as $player){
                $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inlobby"));
                InGameScoreboards::getInstance()->setInLobbyScoreBoard([$player],$lines);
            }
        }
    }
    public function __destruct(){
        $this->sidebar->cancel();
        $this->arena->deleteWorld();
    }
}