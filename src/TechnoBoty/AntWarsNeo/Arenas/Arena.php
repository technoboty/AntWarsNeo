<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use TechnoBoty\AntWarsNeo\GameSession\BaseGameSession;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\MapManager\Map;
use TechnoBoty\AntWarsNeo\MapManager\MapManager;
use TechnoBoty\AntWarsNeo\Scoreboard\InGameScoreboards;
use TechnoBoty\AntWarsNeo\Scoreboard\ScoreboardSession;
use TechnoBoty\AntWarsNeo\SettingsArenas\Settings;
use TechnoBoty\AntWarsNeo\SettingsArenas\SquadSettings;
use TechnoBoty\AntWarsNeo\TeamManager\Team;
use TechnoBoty\AntWarsNeo\TeamManager\TeamGroup;

class Arena {

    //Const
    public const MAX_PLAYERS = 16; //deprecated

    public const WAIT_STAGE = 0;
    public const START_STAGE = 1;
    public const I_STAGE = 2;
    private const II_STAGE = 3;
    private const III_STAGE = 4;

    private const IV_STAGE = 4;

    /** @var Player[] $players */
    private array $players = [];
    private int $stage;

    private bool $lockedFlag = FALSE;

    private Map $arena;

    private TeamGroup $group;

    private TaskHandler $sidebar;

    private BaseGameSession $session;

    private ScoreboardSession $scoreboardSession;

    public function __construct(?Settings $data){
        $this->arena = MapManager::getInstance()->addNewArena();
        $this->stage = self::WAIT_STAGE;
        $this->session = new BaseGameSession($this->arena->getWorld()->getFolderName(),$data);
        $this->group = new TeamGroup($data);
        $this->scoreboardSession = new ScoreboardSession($this->arena->getWorld()->getFolderName());
        $this->scoreboardSession->setCurrentStage(ScoreboardSession::IN_LOBBY);
        $this->scoreboardSession->showInScoreboard();
    }

    public function join(Player $player): void {
        if($this->lockedFlag) {
            return;
        }
        if(!array_key_exists($player->getName(), $this->players)) {
            $this->players[$player->getName()] = $player;
            $player->teleport(new Position(0, 64, 0, $this->arena->getWorld()));
            if(count($this->players) == self::MAX_PLAYERS) {
                $this->lockedFlag = TRUE;
            }
            $this->equip($player);
            $this->onMessage(1, $player);
            if(count($this->players) >= $this->session->getSettingData()::MIN_PLAYERS){
                $this->session->onStart();
                $this->incrementStage();
            }
        }
    }

    public function quit(Player $player): void {
        if(array_key_exists($player->getName(), $this->players)) {
            unset($this->players[$player->getName()]);
        }
        $player->teleport(new Position(0, 100, 0, Server::getInstance()->getWorldManager()->getDefaultWorld()));
        if(count($this->players) == 0) {
            $this->lockedFlag = TRUE;
            ArenaManager::getInstance()->unsetArena($this);
        }
        if($player->isConnected()) {
            if($this->stage == self::WAIT_STAGE) {
                $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inlobby"));
            }
            Main::getInstance()->sendDefaultScoreBoard($player);
        }
        $team = $this->getTeamGroup()->getTeamByPlayer($player);
        $this->getTeamGroup()->quitTeam($player);
        if($team != null && $this->stage != self::WAIT_STAGE && $this->stage != self::START_STAGE && count($team->getPlayers()) == 0){
            $color = $this->getSession()->getSettingData()->colorizeTeamName($team->getTeamName());
            foreach($this->players as $player){
                $player->sendMessage(TextFormat::GRAY."Команда $color".TextFormat::GRAY." истреблена!");
            }
        }
        $this->onMessage(2, $player);
        if(count($this->players) < $this->session->getSettingData()::MIN_PLAYERS){
            $this->session->onStop();
            $this->decrementStage();
        }
    }
    public function getSession() : BaseGameSession{
        return $this->session;
    }

    public function alreadyJoin(): bool {
        return !$this->lockedFlag;
    }

    public function getPlayers(): array {
        return $this->players;
    }

    public function getStage(): int {
        return $this->stage;
    }

    public function getMap(): Map {
        return $this->arena;
    }

    public function equip(Player $player): void {
        $inv = $player->getInventory();
        $player->setGamemode(GameMode::ADVENTURE());
        $inv->clearAll();
        $select = VanillaBlocks::WOOL()->asItem()->setCustomName(TextFormat::BLUE . "Выбрать команду")->setLore(["SummerWorld"]);
        $quit = VanillaBlocks::REDSTONE()->asItem()->setCustomName(TextFormat::RED . "Вернуться в хаб")->setLore(["SummerWorld"]);
        $inv->setItem(2, $select);
        $inv->setItem(6, $quit);
    }

    private function onMessage(int $type, ?Player $player): void {
        switch($type) {
            case 1:
                $count = count($this->players);
                $max = $this->session->getSettingData()::MAX_PLAYERS;
                foreach($this->players as $pl) {
                    $pl->sendMessage(TextFormat::YELLOW . "{$player->getName()} присоиденился " . TextFormat::DARK_PURPLE . "[{$count} / $max]");
                }
                break;
            case 2:
                $count = count($this->players);
                $max = self::MAX_PLAYERS;
                foreach($this->players as $pl) {
                    if($pl->isConnected()) {
                        $pl->sendMessage(TextFormat::YELLOW . "{$player->getName()} вышел " . TextFormat::DARK_PURPLE . "[{$count} / $max]");
                    }
                }
                break;
            case 3:
                break;
        }
    }

    public function getTeamGroup(): TeamGroup {
        return $this->group;
    }
    public function getScoreboard() : ScoreboardSession{
        return $this->scoreboardSession;
    }
    public function incrementStage() : void{
        if($this->stage < self::IV_STAGE){
            $this->stage++;
        }
    }
    public function decrementStage() : void{
        if($this->stage > self::WAIT_STAGE){
            $this->stage--;
        }
    }
    public function __destruct(){
        $this->arena->deleteWorld();
    }
}