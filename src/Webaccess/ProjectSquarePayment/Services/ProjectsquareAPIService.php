<?php

namespace Webaccess\ProjectSquarePayment\Services;

use GuzzleHttp\Client;
use Webaccess\ProjectSquarePayment\Repositories\PlatformRepository;

class ProjectsquareAPIService implements ProjectsquareAPI
{
    private $platformRepository;
    private $apiToken;

    public function __construct(PlatformRepository $platformRepository, $apiToken)
    {
        $this->platformRepository = $platformRepository;
        $this->apiToken = $apiToken;
    }

    /**
     * @param $platformID
     * @return mixed
     */
    public function getUsersLimit($platformID)
    {
        $client = new Client(['base_uri' => $this->getPlatformURL($platformID)]);
        $response = $client->get('/api/users_count');
        $body = json_decode($response->getBody());

        return $body->count;
    }

    /**
     * @param $platformID
     * @param $usersCount
     */
    public function updateUsersLimit($platformID, $usersCount)
    {
        $client = new Client(['base_uri' => $this->getPlatformURL($platformID)]);
        $response = $client->post('/api/update_users_count', [
            'json' => [
                'count' => $usersCount,
                'token' => $this->apiToken
            ]
        ]);
    }

    private function getPlatformURL($platformID)
    {
        if ($platform = $this->platformRepository->getByID($platformID)) {
            return 'http://' . $platform->getSlug() . '.projectsquare.io';
        }

        return false;
    }
}