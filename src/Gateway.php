<?php

namespace Uniguide\Pportalen;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Uniguide\Pportalen\DataTransferObjects\DepartmentDTO;
use Uniguide\Pportalen\DataTransferObjects\ResolvedTokenDTO;
use Uniguide\Pportalen\DataTransferObjects\UserDTO;

class Gateway
{

    /**@var Client * */
    protected static $client;

    protected static function withClient(): Client
    {
        if (self::$client) {
            return self::$client;
        }
        self::$client = new Client([
            'base_uri' => config('services.pportalen.endpoint'),
            'headers' => [
                'Access-Token' => config('services.pportalen.access_token')
            ]
        ]);

        return self::$client;
    }


    protected static function jsonDecodeResponse(ResponseInterface $res): array
    {
        return json_decode($res->getBody()->getContents(), true);
    }

    protected static function get($url): ResponseInterface
    {
        return self::withClient()->get($url);
    }

    protected static function post($url): ResponseInterface
    {
        return self::withClient()->post($url);
    }

    public static function resolveToken(string $tmpToken): ResolvedTokenDTO
    {
        $res = self::withClient()->get(sprintf("application/%s/resolve-tmp-token/%s", config('services.pportalen.app_id'), $tmpToken));

        return new ResolvedTokenDTO(self::jsonDecodeResponse($res));
    }

    /**
     * @return Collection[]
     */
    public static function getUsers(): Collection
    {
        $res = self::withClient()->get(sprintf("application/%s/users", config('services.pportalen.app_id')));

        return collect(array_map(function ($row) {
            return new UserDTO($row);
        }, self::jsonDecodeResponse($res)));

    }

    /**
     * @return Collection[]
     */
    public static function getDepartments(): Collection
    {
        $res = self::withClient()->get(sprintf("application/%s/departments", config('services.pportalen.app_id')));

        return collect(array_map(function ($row) {
            return new DepartmentDTO($row);
        }, self::jsonDecodeResponse($res)));

    }

    public static function triggerFullSync(): void
    {
        self::withClient()->post(sprintf("application/%s/trigger-full-sync", config('services.pportalen.app_id')));
    }
}
