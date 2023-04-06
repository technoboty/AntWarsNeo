<?php

namespace TechnoBoty\AntWarsNeo\TeamManager;

class TeamManager{

    /** @var Team[] $teams */
    private array $teams;

    public function __construct($teams = []){
        $this->teams = $teams;
    }
    public function addTeam(Team $team) : void{
        if(!$this->teamExists($team)){
            $this->teams[] = $team;
        }
    }
    public function teamExists(Team $team) : bool{
        foreach($this->teams as $value){
            if($value->getTeamNumber() == $team->getTeamNumber()){
                return true;
            }
        }
        return false;
    }
}