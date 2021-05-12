<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Exercise;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ExerciseTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', 'http://0.0.0.0:8000/api/exercises');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Exercise',
            '@id' => '/api/exercises',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 500,
            'hydra:view' => [
                '@id' => '/api/exercises?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/exercises?page=1',
                'hydra:last' => '/api/exercises?page=17',
                'hydra:next' => '/api/exercises?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(Exercise::class);
    }

    public function testCreateExercise(): void
    {
        $response = static::createClient()->request('POST', 'http://0.0.0.0:8000/api/exercises', ['json' => [
            "exerciseName" => "test",
            "plan" => "/api/plans/400"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "exerciseName" => "test",
            "plan" => "/api/plans/400"
        ]);
        $this->assertRegExp('~^/api/exercises/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Exercise::class);
    }

    public function testUpdateExercise(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Exercise::class, ['id' => 500]);

        $client->request('PUT', $iri, ['json' => [
            'exerciseName' => 'updated title',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'exerciseName' => 'updated title'
        ]);
    }

    public function testDeleteExercise(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Exercise::class, ['id' => 500]);
        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(Exercise::class)->findOneBy(['id' => 500])
        );
    }
}
