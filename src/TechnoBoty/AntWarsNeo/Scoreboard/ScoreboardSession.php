<?php

namespace TechnoBoty\AntWarsNeo\Scoreboard;

use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Main;

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
                    if($arena->getScoreboard()->getCurrentStage() != ScoreboardSession::IN_LOBBY){$this->getHandler()->cancel();}
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
                InGameScoreboards::getInstance()->setInLobbyScoreBoard($players,$this->getLobbyLines());
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
}