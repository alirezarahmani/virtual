<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * ExerciseInstances
 * @ApiResource()
 * @ORM\Table(name="exercise_instances")
 * @ORM\Entity
 */
class ExerciseInstances
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    public $id;

    /**
     * @var int
     *
     * @ORM\Column(name="exercise_duration", type="integer", nullable=false, options={"comment"="duration in seconds"})
     */
    public $exerciseDuration = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="in_order", type="integer", nullable=false)
     */
    public $order = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Exercise::class, inversedBy="exerciseItems")
     */
    public $exercise;

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): self
    {
        $this->exercise = $exercise;

        return $this;
    }


}
