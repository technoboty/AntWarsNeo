<?php

namespace TechnoBoty\AntWarsNeo\Scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\SettingsArenas\SquadSettings;

class ScoreboardSession{

    public const IN_LOBBY = "inlobby";
    public const IN_I_STAGE = "1stage";
    public const IN_II_STAGE = "2stage";
    public const IN_III_STAGE = "3stage";
    public const IN_IV_STAGE = "4stage";


    private string $currentScoreboard;

    public function __construct(private string $arenaName){}

    public function showInScoreboard() : void{
        if(!isset($this->currentScoreboard)){return;}
        Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new class($this->arenaName,$this) extends Task{

            public function __construct(private string $arenaName){}

            public function onRun(): void{
                $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
                if($arena != null){
                    $arena->getScoreboard()->_show($arena->getPlayers());
                } else {
                    $this->getHandler()->cancel();
                }
            }
        },20,20);

    }
    public function setCurrentStage(string $stage) : void{
        $this->currentScoreboard = $stage;
    }
    public function getCurrentStage() : string{
        return $this->currentScoreboard;
    }

    /**
     * @internal
     * @param array $players
     * @return void
     */
    public function _show(array $players) : void{
        switch($this->currentScoreboard){
            case self::IN_LOBBY:
                InGameScoreboards::getInstance()->removeInLobbyScoreboard($players);
                InGameScoreboards::getInstance()->setInGameScoreboard($players,$this->getLobbyLines(),"inlobby");
                break;
            case self::IN_I_STAGE:
                InGameScoreboards::getInstance()->removeInLobbyScoreboard($players);
                foreach(ArenaManager::getInstance()->getArenaByWorldName($this->arenaName)?->getPlayers() as $player){
                    $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("1stage"));
                    InGameScoreboards::getInstance()->setInGameScoreboard($players, $this->getIstageLines($player), "1stage");
                }
                break;
        }
    }
    private function getLobbyLines(): array {
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        $lobby = TextFormat::YELLOW . $arena?->getMap()->getWorld()->getFolderName();
        $count = count($arena->getPlayers());
        $max = $arena->getSession()->getSettingData()::MAX_PLAYERS;
        $status = TextFormat::DARK_PURPLE . "Ожидание игры ";
        $donate = TextFormat::GOLD . "summer-world.me ";
        $clock = $arena->getSession()->getTimer();
        $lines = [
            1 => "Лобби: $lobby ",
            2 => "Статус: $status ",
            3 => "Игроков:" . TextFormat::RED . " $count / $max ",
            4 => "До старта: ".TextFormat::GOLD.$clock." сек.",
            5 => "Донат: $donate "
        ];
        return $lines;
    }
    private function getIstageLines(Player $player) : array{
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        $team = (new SquadSettings())->colorizeTeamName($arena->getTeamGroup()->getTeamByPlayer($player)->getTeamName());
        $lobby = TextFormat::YELLOW . $arena?->getMap()->getWorld()->getFolderName();
        $data = $arena->getSession()->getSettingData();
        $timer = $arena->getSession()->getTimer();
        $lines = [
            1 => "Лобби: $lobby ",
            2 => "Ваша команда $team ",
            3 => "Стадия: ".TextFormat::GOLD."1 подсветка ",
            4 => "До подсветки: ".TextFormat::RED."$timer "."сек.",
            5 => "Живые команды: "
        ];
        foreach($arena->getTeamGroup()->getTeams() as $team){
            if(count($team->getPlayers()) > 0){
                $count = count($team->getPlayers());
                $lines[] = " - ".$data->colorizeTeamName($team->getTeamName())." ($count)  ";
            }
        }
        $lines[] = "Донат: ".TextFormat::GOLD."summer-world.me  ";
        return $lines;
    }
}