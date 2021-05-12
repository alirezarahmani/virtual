<?php

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\ExerciseInstances;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ExerciseInstancesTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', 'http://0.0.0.0:8000/api/exercise_instances');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/ExerciseInstances',
            '@id' => '/api/exercise_instances',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 500,
            'hydra:view' => [
                '@id' => '/api/exercise_instances?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/exercise_instances?page=1',
                'hydra:last' => '/api/exercise_instances?page=17',
                'hydra:next' => '/api/exercise_instances?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);
        $this->assertMatchesResourceCollectionJsonSchema(ExerciseInstances::class);
    }

    public function testCreateExerciseInstances(): void
    {
        $response = static::createClient()->request('POST', 'http://0.0.0.0:8000/api/exercise_instances', ['json' => [
            "exerciseDuration" => 0,
            "order" => 0,
            "exercise" => "/api/exercises/400"
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            "exerciseDuration" => 0,
            "order" => 0,
            "exercise" => "/api/exercises/400"
        ]);
        $this->assertRegExp('~^/api/exercise_instances/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(ExerciseInstances::class);
    }

    public function testUpdateExerciseInstances(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(ExerciseInstances::class, ['id' => 500]);

        $client->request('PUT', $iri, ['json' => [
            'exerciseDuration' => 10,
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'exerciseDuration' => 10
        ]);
    }

    public function testDeleteExerciseInstances(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(ExerciseInstances::class, ['id' => 500]);
        $client->request('DELETE', $iri);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            static::$container->get('doctrine')->getRepository(ExerciseInstances::class)->findOneBy(['id' => 500])
        );
    }
}
