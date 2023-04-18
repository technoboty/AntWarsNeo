<?php

namespace TechnoBoty\AntWarsNeo\GameSession;

use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\SettingsArenas\Settings;

final class BaseGameSession
{

    private bool $isStarted = FALSE;

    private int $secondPerStart = 60;

    private TaskHandler $handler;

    public function __construct(private string $arenaName, private ?Settings $data){
        //NOOP
    }

    public function isStarted(): bool{
        return $this->isStarted;
    }

    public function onStart(): void{
        if ($this->isStarted) {
            return;
        }
        $this->isStarted = true;
        $this->setWaitStartHandler();
    }

    public function onStop(): void{
        if (!$this->isStarted) {
            return;
        }
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        if ($arena->getStage() != Arena::START_STAGE) {
            return;
        }
        $this->isStarted = false;
        $this->secondPerStart = 60;
    }
    public function getTimer() : int{
        return $this->secondPerStart;
    }
    public function getSettingData() : Settings{
        return $this->data;
    }

    private function setWaitStartHandler(): void{
        $this->handler = Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new class($this->arenaName) extends Task {

            public function __construct(private string $arenaName){}

            public function onRun(): void{
                $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
                if ($arena != null) {
                    if($arena->getSession()->getTimer() > 0) {
                        $arena->getSession()->decrementTimer();
                    } else {
                        //TODO
                        $this->getHandler()->cancel();
                        var_dump("Игра типо началась");
                    }
                } else {
                    $this->getHandler()->cancel();
                }
            }
        }, 20, 20);
    }

    public function decrementTimer() : void{
        $this->secondPerStart--;
    }
}