<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Plan;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class PlanDayTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', 'http://0.0.0.0:8000/api/plan_days');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Plan',
            '@id' => '/api/plan_days',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 500,
            'hydra:view' => [
                '@id' => '/api/plan_days?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/plan_days?page=1',
                'hydra:last' => '/api/plan_days?page=17',
                'hydra:next' => '/api/plan_days?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Plan::class);
    }

    public function testCreatePlan(): void
    {
        $response = static::createClient()->request('POST', 'http://0.0.0.0:8000/api/plan_days', ['json' => [
            "dayName" => "string",
            "order" => 0,
            "plan" => "/api/plans/400"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "dayName" => "string",
            "order" => 0,
            "plan" => "/api/plans/400"
        ]);
        $this->assertRegExp('~^/api/plan_days/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Plan::class);
    }

    public function testUpdatePlan(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Plan::class, ['id' => 500]);

        $client->request('PUT', $iri, ['json' => [
            'dayName' => 'updated title',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'dayName' => 'updated title'
        ]);
    }

    public function testDeletePlan(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Plan::class, ['id' => 500]);
        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(Plan::class)->findOneBy(['id' => 500])
        );
    }
}
