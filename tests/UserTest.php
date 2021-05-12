<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', 'http://0.0.0.0:8000/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/User',
            '@id' => '/api/users',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 500,
            'hydra:view' => [
                '@id' => '/api/users?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/users?page=1',
                'hydra:last' => '/api/users?page=17',
                'hydra:next' => '/api/users?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(User::class);
    }

    public function testCreateUser(): void
    {
        $response = static::createClient()->request('POST', 'http://0.0.0.0:8000/api/users', ['json' => [
            "name" => "test",
            "lastName" => "test string",
            "phone" => "554554",
            "email" => "alireza@test.com",
            "plan" => "/api/plans/400"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "name" => "test",
            "lastName" => "test string",
            "phone" => "554554",
            "email" => "alireza@test.com",
            "plan" => "/api/plans/400"
        ]);
        $this->assertRegExp('~^/api/users/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }

    public function testUpdateUser(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(User::class, ['id' => 500]);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'updated title',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated title'
        ]);
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(User::class, ['id' => 500]);
        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(User::class)->findOneBy(['id' => 500])
        );
    }
}
