<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Plan;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class PlanTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', 'http://0.0.0.0:8000/api/plans');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Plan',
            '@id' => '/api/plans',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 500,
            'hydra:view' => [
                '@id' => '/api/plans?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/plans?page=1',
                'hydra:last' => '/api/plans?page=17',
                'hydra:next' => '/api/plans?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Plan::class);
    }

    public function testCreateBook(): void
    {
        $response = static::createClient()->request('POST', 'http://0.0.0.0:8000/api/plans', ['json' => [
            "planName" => "test",
            "planDescription" => "test string",
            "planDifficulty" => 1
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "planName" => "test",
            "planDescription" => "test string",
            "planDifficulty" => 1,
            "exercise" => [
            ],
            "planDays" => [
            ],
            "users" => [
            ]
        ]);
        $this->assertRegExp('~^/api/plans/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Plan::class);
    }

    public function testUpdatePlan(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Plan::class, ['id' => 500]);

        $client->request('PUT', $iri, ['json' => [
            'planName' => 'updated title',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'planName' => 'updated title'
        ]);
    }

    public function testDeletePlan(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Plan::class, ['id' => 500]);
        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(Plan::class)->findOneBy(['planName' => 'test'])
        );
    }
}
