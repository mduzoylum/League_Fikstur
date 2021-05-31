<?php

namespace App\Http\Controllers;

use App\Models\Hasmatch;
use App\Models\Manager;
use App\Models\Match;
use App\Models\Player;
use App\Models\Result;
use App\Models\Team;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public $round;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('index', ['Fixture' => session('fixtureList')]);
    }

    /**
     * @return mixed
     */
    public function playMatch()
    {
        $matchCount = Match::count();
        if ($matchCount == 0) {
            $this->round = 1;
        } else {
            $matchTable = Match::select("round")->orderByDesc("round")->first();
            $this->round = $matchTable->round + 1;
        }
        $getMatchs = Hasmatch::where("round", $this->round)->get();
        foreach ($getMatchs as $getMatch) {
            //todo :  Ev Sahibi Takım Oyuncuları
            $getPlayers = Player::where("team_id", $getMatch->home_team)->get();

            //todo : Anlık performansları ile Takım gücü hesaplanır
            $playersOverall = 0;
            foreach ($getPlayers as $getPlayer) {
                $playersOverall += $getPlayer->overall * $this->getPerformance();
            }
            $homePlayersOverall = $playersOverall / count($getPlayers);

            //todo : Manager Puanı Eklenir
            $getManager = Manager::where("team_id", $getMatch->home_team)->first();
            $homeManagerOverall = $getManager->overall * config('enums.MANAGER_MULTIPLIER');

            //todo : İç Saha Puanı
            $homeSideOverall = config('enums.HOME_MULTIPLIER');

            //todo : Ev Sahibi Takım Toplam Puanı
            $homeTeamOveral = $homePlayersOverall + $homeManagerOverall + $homeSideOverall;

            //////////////////////////////////////////////////////////

            //todo : Deplasman Takım Oyuncuları
            $getPlayers = Player::where("team_id", $getMatch->away_team)->get();

            //todo : Anlık performansları ile Takım gücü hesaplanır
            $playersOverall = 0;
            foreach ($getPlayers as $getPlayer) {
                $playersOverall += $getPlayer->overall * $this->getPerformance();
            }
            $awayPlayersOverall = $playersOverall / count($getPlayers);

            //todo : Manager Puanı Eklenir
            $getManager = Manager::where("team_id", $getMatch->away_team)->first();
            $awayManagerOverall = $getManager->overall * config('enums.MANAGER_MULTIPLIER');

            //todo : İç Saha Puanı
            $awaySideOverall = config('enums.AWAY_MULTIPLIER');

            //todo : Deplasman Takım Toplam Puanı
            $awayTeamOveral = $awayPlayersOverall + $awayManagerOverall + $awaySideOverall;

            /////////////////////////////////////////////////////////////////

            if ($homeTeamOveral > $awayTeamOveral) {
                $check = true;
                while ($check) {
                    $homeScore = rand(1, 5);
                    $awayScore = rand(1, 5);
                    if ($homeScore >= $awayScore) {

                        $match = new Match();
                        $match->round = $this->round;
                        $match->home_team = $getMatch->home_team;
                        $match->home_team_score = $homeScore;
                        $match->home_team_performance = $homeTeamOveral;
                        $match->home_team_is_win = $homeScore > $awayScore ? 1 : 0;
                        $match->away_team = $getMatch->away_team;
                        $match->away_team_score = $awayScore;
                        $match->away_team_performance = $awayTeamOveral;
                        $match->away_team_is_win = 0;
                        $match->is_draw = $homeScore == $awayScore ? 1 : 0;
                        $match->save();

                        $check = false;
                    }
                }
            } else {
                $check = true;
                while ($check) {
                    $homeScore = rand(1, 5);
                    $awayScore = rand(1, 5);
                    if ($awayScore >= $homeScore) {

                        $match = new Match();
                        $match->round = $this->round;
                        $match->home_team = $getMatch->home_team;
                        $match->home_team_score = $homeScore;
                        $match->home_team_performance = $homeTeamOveral;
                        $match->home_team_is_win = 0;
                        $match->away_team = $getMatch->away_team;
                        $match->away_team_score = $awayScore;
                        $match->away_team_performance = $awayTeamOveral;
                        $match->away_team_is_win = $awayScore > $homeScore ? 1 : 0;
                        $match->is_draw = $homeScore == $awayScore ? 1 : 0;
                        $match->save();

                        $check = false;
                    }
                }
            }
        }
        $this->getPointList();

        $getMatchScore = Match::get();
        $getMatchScore->map(function ($match) {
            $hometeam = Team::where("id", $match->home_team)->first();
            $awayteam = Team::where("id", $match->away_team)->first();

            $match->home_team_name = $hometeam->team_name;
            $match->away_team_name = $awayteam->team_name;

        });

        return $getMatchScore->toJson();
    }

    /**
     * @return mixed
     */
    public function getMatchScore()
    {
        $getMatchScore = Match::get();
        $getMatchScore->map(function ($match) {
            $hometeam = Team::where("id", $match->home_team)->first();
            $awayteam = Team::where("id", $match->away_team)->first();

            $match->home_team_name = $hometeam->team_name;
            $match->away_team_name = $awayteam->team_name;

        });
        return $getMatchScore->toJson();
    }

    /**
     * getPointList
     */
    public function getPointList()
    {
        $getMatchs = Match::where('round', $this->round)->get();

        foreach ($getMatchs as $getMatch) {
            $homePoints = 0;
            $awayPoints = 0;
            $homeIsWin = 0;
            $awayIsWin = 0;
            $isDraw = 0;

            if ($getMatch->home_team_is_win == 1) {
                $homePoints += 3;
                $homeIsWin = 1;
            }
            if ($getMatch->is_draw == 1) {
                $homePoints += 1;
                $awayPoints += 1;
                $isDraw = 1;
            }
            if ($getMatch->away_team_is_win == 1) {
                $awayPoints += 3;
                $awayIsWin = 1;
            }

            $homeTeam = Result::where("team_id", $getMatch->home_team)->get();
            foreach ($homeTeam as $home) {
                $home->play = $home->play + 1;
                $home->win = $homeIsWin == 1 ? $home->win + 1 : $home->win;
                $home->draw = $isDraw == 1 ? $home->draw + 1 : $home->draw;
                $home->lost = $awayIsWin == 1 ? $home->lost + 1 : $home->lost;
                $home->gf = $home->gf + $getMatch->home_team_score;
                $home->ga = $home->ga + $getMatch->away_team_score;
                $home->gd = $home->gf - $home->ga;
                $home->pts = $home->pts + $homePoints;
                $home->save();
            }

            $awayTeam = Result::where("team_id", $getMatch->away_team)->get();
            foreach ($awayTeam as $away) {
                $away->play = $away->play + 1;
                $away->win = $awayIsWin == 1 ? $away->win + 1 : $away->win;
                $away->draw = $isDraw == 1 ? $away->draw + 1 : $away->draw;
                $away->lost = $homeIsWin == 1 ? $away->lost + 1 : $away->lost;
                $away->gf = $away->gf + $getMatch->away_team_score;
                $away->ga = $away->ga + $getMatch->home_team_score;
                $away->gd = $away->gf - $away->ga;
                $away->pts = $away->pts + $awayPoints;
                $away->save();
            }
        }
    }

    /**
     * @return float|int
     */
    public function getPerformance()
    {
        $result = 1;
        $performance = array_rand(config('enums.PERFORMANCE'), 1);
        switch ($performance) {
            case 0://Kötü Performance
                $result = 0.80;
                break;
            case 1://Kendi Performansı
                $result = 1;
                break;
            case 2://Yüksek Performans
                $result = 1.20;
                break;
        }
        return $result;
    }

    /**
     * getPointsTable
     */
    public function getPointsTable()
    {
        $getPointsTable = Result::with('team')->orderByDesc('pts')->orderByDesc('gd')->orderByDesc('gf')->get()->toJson();
        echo $getPointsTable;
    }

    /**
     * clearFixture
     */
    public function clearFixture()
    {
        Hasmatch::query()->truncate();
        Match::query()->truncate();
        Result::query()->truncate();
    }


}
