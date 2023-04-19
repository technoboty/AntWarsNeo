<?php

namespace TechnoBoty\AntWarsNeo\GameSession;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskHandler;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\sound\XpCollectSound;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\SettingsArenas\Settings;
use TechnoBoty\AntWarsNeo\TeamManager\Team;

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
                    if($arena->getSession()->getTimer() > 0){
                        $arena->getSession()->decrementTimer();
                        if($arena->getSession()->getTimer() <= 10){
                            foreach($arena->getPlayers() as $player){
                                $player->getWorld()->addSound($player->getPosition(),new XpCollectSound(),[$player]);
                            }
                        }
                    } else {
                        $this->getHandler()->cancel();
                        $arena->incrementStage();
                        $arena->getTeamGroup()->equipTeams($arena->getPlayers());
                        $arena->getScoreboard()->setCurrentStage("1stage");
                        $arena->getSession()->startStack();
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
    public function startStack() : void{
        $this->teleport();
        $this->selectStartGamemode();
        $this->addStartTips();
        $this->TipsTimer();
    }
    public function teleport() : void{
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        if($arena == null){return;}
        foreach($arena->getTeamGroup()->getTeams() as $team){
            /** @var Team $team */
            $spawn = $this->data::TEAM_SPAWN_LOCATION[$team->getTeamName()];
            foreach($team->getPlayers() as $player){
                /** @var Player $player */
                $player->teleport(new Position($spawn[0],$spawn[1],$spawn[2],$arena->getMap()->getWorld()));
            }
        }
    }
    public function selectStartGamemode() : void{
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        if($arena == null){return;}
        foreach($arena->getPlayers() as $player){
            $player->setGamemode(GameMode::SURVIVAL());
        }
    }
    public function addStartTips() : void{
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        if($arena == null){return;}
        foreach($arena->getPlayers() as $player){
            $this->startEffects($player);
            $player->getInventory()->clearAll();
            $player->setImmobile();
        }
    }
    private function startEffects(Player $player) : void{
        $player->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(),100,2,false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::MINING_FATIGUE(),100,2,false));
        $player->getEffects()->add(new EffectInstance(VanillaEffects::SLOWNESS(),100,2,false));
    }
    private function TipsTimer() : void{
        $arena = ArenaManager::getInstance()->getArenaByWorldName($this->arenaName);
        if($arena == null){return;}
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new class($arena->getPlayers()) extends Task{

            private int $iteration = 5;
            public function __construct(private array $players){}
            public function onRun(): void{
                if($this->iteration != 0){
                    switch($this->iteration){
                        case 5:
                            foreach($this->players as $player){
                                /** @var Player $player */
                                $player->sendTitle("5",TextFormat::GOLD."Копайте ресурсы!",-1,20,0);
                                $this->iteration--;
                            }
                            break;
                        case 4:
                            foreach($this->players as $player){
                                /** @var Player $player */
                                $player->sendTitle("4",TextFormat::GOLD."Крафтите броню и инструменты!",-1,20,0);
                                $this->iteration--;
                            }
                            break;
                        case 3:
                            foreach($this->players as $player){
                                /** @var Player $player */
                                $player->sendTitle("3",TextFormat::GOLD."Готовтесь к бою!",-1,20,0);
                                $this->iteration--;
                            }
                            break;
                        case 2:
                            foreach($this->players as $player){
                                /** @var Player $player */
                                $player->sendTitle("2",TextFormat::GOLD."Остантесь последним вижившим!",-1,20,0);
                                $this->iteration--;
                            }
                            break;
                        case 1:
                            foreach($this->players as $player){
                                /** @var Player $player */
                                $player->sendTitle("1",TextFormat::GOLD."!Союз вражеских комманд запрещен!",-1,10,0);
                                $this->iteration--;
                            }
                            break;
                    }
                } else {
                    foreach($this->players as $player){
                        /** @var Player $player */
                        $player->setImmobile(false);
                        $player->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(),20 * 60 * 45,1,false));
                    }
                    $this->getHandler()->cancel();
                }
            }
        },20);
    }
}