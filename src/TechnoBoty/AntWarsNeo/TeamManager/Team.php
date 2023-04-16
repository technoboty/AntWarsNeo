<?php

namespace TechnoBoty\AntWarsNeo\TeamManager;

use pocketmine\player\Player;

class Team{

    private string $teamName;

    private array $players;

    private int $maxPlayers;

    public function __construct(string $name,int $maxPlayers,$players = []){
        $this->teamName = $name;
        $this->players = $players;
        $this->maxPlayers = $maxPlayers;
    }
    public function getTeamName() : string{
        return $this->teamName;
    }
    public function getMaxPlayers() : int{
        return $this->maxPlayers;
    }
    public function getPlayers() : array{
        return $this->players;
    }
    public function addPlayer(Player $player) : void{
        if(!in_array($player,$this->players)){
            $this->players[$player->getName()] = $player;
        }
    }
    public function removePlayer(Player $player) : void{
        if(array_key_exists($player->getName(),$this->players)){
            unset($this->players[$player->getName()]);
        }
    }
}