<?php

namespace TechnoBoty\AntWarsNeo\TeamManager;

use pocketmine\player\Player;

class Team{

    private int $teamNumber;

    private array $players;

    private int $masPlayers;

    public function __construct(int $teamNumber,int $maxPlayers,$players = []){
        $this->teamNumber = $teamNumber;
        $this->players = $players;
        $this->masPlayers = $maxPlayers;
    }
    public function getTeamNumber() : int{
        return $this->teamNumber;
    }
    public function getMaxPlayers() : int{
        return $this->masPlayers;
    }
    public function getPlayers() : array{
        return $this->players;
    }
    public function addPlayer(Player $player) : void{
        if(!in_array($player,$this->players)){
            $this->players[] = $player;
        }
    }
    public function removePlayer(Player $player) : void{
        if(in_array($player,$this->players)){
            unset($this->players[array_search($player,$this->players)]);
        }
    }
}