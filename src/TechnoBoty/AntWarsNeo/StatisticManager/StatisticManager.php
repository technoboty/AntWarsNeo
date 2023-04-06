<?php

namespace TechnoBoty\AntWarsNeo\StatisticManager;

use JsonException;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use TechnoBoty\AntWarsNeo\Main;

class StatisticManager{

    private array $default = [0,0,0,0];

    /** @var string[][] $statistic */
    private array $statistic;

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
        $this->statistic = (new Config(Main::getInstance()->getDataFolder()."statistic/mainStatistic.json",Config::JSON))->get("stats");
    }
    public function getAllStatistic() : array{
        return $this->statistic;
    }
    public function getCurrentStatistic(string $plName) : array{
        if(array_key_exists($plName,$this->statistic)){
            return $this->statistic[$plName];
        } else {
            return $this->default;
        }
    }
    public function updateCurrentStatistic(string $plName,array $stats) : void{
        $this->statistic[$plName] = $stats;
    }

    /**
     * @throws JsonException
     */
    public function __destruct(){
        $cfg = new Config(Main::getInstance()->getDataFolder()."statistic/mainStatistic.json",Config::JSON);
        $cfg->set("stats",$this->statistic);
        $cfg->save();
    }
}