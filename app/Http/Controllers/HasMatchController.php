<?php

namespace App\Http\Controllers;

use App\Models\Hasmatch;
use App\Models\Result;
use App\Models\Team;

class HasMatchController extends Controller
{

    private $teams;
    private $countGames;
    private $pair;
    private $odd;
    private $countTeams;

    /**
     * MatchController constructor.
     */
    public function __construct()
    {
        Hasmatch::query()->truncate();
        $this->teams = Team::get()->toArray();
        if (is_array($this->teams)) {
            shuffle($this->teams);
            $this->countTeams = count($this->teams);
            if ($this->countTeams % 2 == 1) {
                $this->countTeams++;
                $teams[] = "free this round";
            }
            $this->countGames = floor($this->countTeams / 2);
            for ($i = 0; $i < $this->countTeams; $i++) {
                $this->aux[] = $this->teams[$i];
            }
        } else {
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function init()
    {
        for ($x = 0; $x < $this->countGames; $x++) {
            $this->pair[$x][0] = $this->aux[$x];
            $this->pair[$x][1] = $this->aux[($this->countTeams - 1) - $x];
        }
        return $this->pair;
    }

    /**
     * @return array
     */
    public function getSchedule()
    {
        $rol = array();
        $rol[] = $this->init();
        for ($y = 1; $y < $this->countTeams - 1; $y++) {
            if ($y % 2 == 0) {
                $rol[] = $this->getPairRound();
            } else {
                $rol[] = $this->getOddRound();
            }
        }
        return $rol;
    }


    /**
     * @return mixed
     */
    public function getPairRound()
    {
        for ($z = 0; $z < $this->countGames; $z++) {
            if ($z == 0) {
                $this->pair[$z][0] = $this->odd[$z][0];
                $this->pair[$z][1] = $this->odd[$z + 1][0];
            } elseif ($z == $this->countGames - 1) {
                $this->pair[$z][0] = $this->odd[0][1];
                $this->pair[$z][1] = $this->odd[$z][1];
            } else {
                $this->pair[$z][0] = $this->odd[$z][1];
                $this->pair[$z][1] = $this->odd[$z + 1][0];
            }
        }
        return $this->pair;
    }

    /**
     * @return mixed
     */
    public function getOddRound()
    {
        for ($j = 0; $j < $this->countGames; $j++) {
            if ($j == 0) {
                $this->odd[$j][0] = $this->pair[$j][1];
                $this->odd[$j][1] = $this->pair[$this->countGames - 1][0]; //Pivot
            } else {
                $this->odd[$j][0] = $this->pair[$j][1];
                $this->odd[$j][1] = $this->pair[$j - 1][0];
            }
        }
        return $this->odd;
    }

    /**
     * Create Fixture
     */
    public function createFixture()
    {
        $fixtureList = "";
        $schedule = $this->getSchedule();
        $i = 1;
        foreach ($schedule as $rounds) {
            $fixtureList .= "<ul class=\"list-group\">";
            $fixtureList .= "<li class=\"list-group-item active\" aria-current=\"true\">Round {$i}</li>";
            foreach ($rounds as $game) {
                $fixtureList .= "<li class=\"list-group-item\" style='height: 35px;'>{$game[0]["team_name"]} vs {$game[1]["team_name"]}</li>";
                Hasmatch::create([
                    "round" => $i,
                    "home_team" => $game[0]["id"],
                    "away_team" => $game[1]["id"],
                ]);
            }
            $fixtureList .= "</ul>";
            $i++;
        }

        foreach ($schedule as $rounds) {
            $fixtureList .= "<ul class=\"list-group\">";
            $fixtureList .= "<li class=\"list-group-item active\" aria-current=\"true\">Round {$i}</li>";
            foreach ($rounds as $game) {
                $fixtureList .= "<li class=\"list-group-item\" style='height: 35px;'>{$game[1]["team_name"]} vs {$game[0]["team_name"]}</li>";
                Hasmatch::create([
                    "round" => $i,
                    "home_team" => $game[1]["id"],
                    "away_team" => $game[0]["id"],
                ]);
            }
            $fixtureList .= "</ul>";
            $i++;
        }

        $this->createPointList();
        return json_encode($fixtureList);
    }

    /**
     * createPointList
     */
    public function createPointList()
    {
        if (Result::count() == 0) {
            foreach ($this->teams as $team) {
                Result::create(["team_id" => $team["id"]]);
            }
        }
    }
}
