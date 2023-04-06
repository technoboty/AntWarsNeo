<?php

namespace TechnoBoty\AntWarsNeo\StatisticManager;

use pocketmine\player\Player;

class PlayerStatistic{

    private array $stat;

    private string $name;

    public function __construct(Player $player){
        $nick = $player->getName();
        $this->name = $nick;
        $this->stat = StatisticManager::getInstance()->getCurrentStatistic($nick);
    }
    public function getStatistic() : array{
        return $this->stat;
    }

    /**
     * @param int $stat - константа статы
     * @return void
     */
    public function updateStats(int $stat) : void{
        $statistic = $this->stat;
        $this->stat[$stat] = $statistic[$stat]++;
    }

    public function __destruct(){
        StatisticManager::getInstance()->updateCurrentStatistic($this->name,$this->stat);
    }
}