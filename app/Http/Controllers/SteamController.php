<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class SteamController extends Controller
{
    protected Client $client;
    public $id = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        return view('index');
    }

    public function search(Request $request)
    {
        $this->id = $request->q;

        $result['GetPlayerSummaries'] = $this->getPlayerSummaries();
        $result['GetOwnedGames'] = $this->getOwnedGames();
        $result['GetRecentlyPlayedGames'] = $this->getRecentlyPlayedGames();
        $result['GetFriendList'] = $this->getFriendList();

        return view('index', compact('result'));
    }

    public function getPlayerSummaries()
    {
        $resGetPlayerSummaries = $this->client->request('GET', 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/', [
            'query' => [
                'key' => env('STEAM_API_KEY'),
                'steamids' => $this->id
            ]
        ])->getBody()->getContents();

        $resGetPlayerSummaries = json_decode($resGetPlayerSummaries);
        return $resGetPlayerSummaries?->response?->players[0] ?? null;
    }

    public function getOwnedGames()
    {
        try {
            $resGetOwnedGames = $this->client->request('GET', 'http://api.steampowered.com/IPlayerService/GetOwnedGames/v0001/', [
                'query' => [
                    'key' => env('STEAM_API_KEY'),
                    'steamid' => $this->id
                ]
            ])->getBody()->getContents();
            $resGetOwnedGames = json_decode($resGetOwnedGames);
            return $resGetOwnedGames?->response ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getFriendList()
    {
        try {
            $resGetFriendList = $this->client->request('GET', 'http://api.steampowered.com/ISteamUser/GetFriendList/v0001/', [
                'query' => [
                    'key' => env('STEAM_API_KEY'),
                    'steamid' => $this->id
                ]
            ])->getBody()->getContents();

            $resGetFriendList = json_decode($resGetFriendList);
            return $resGetFriendList?->friendslist?->friends ?? 0;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getRecentlyPlayedGames()
    {
        try {
            $resGetRecentlyPlayedGames = $this->client->request('GET', 'http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/', [
                'query' => [
                    'key' => env('STEAM_API_KEY'),
                    'steamid' => $this->id
                ]
            ])->getBody()->getContents();

            $resGetRecentlyPlayedGames = json_decode($resGetRecentlyPlayedGames);
            return $resGetRecentlyPlayedGames?->response ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
