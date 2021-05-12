<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource()]
/**
 * Exercise
 * @ORM\Table(name="exercise")
 * @ORM\Entity
 */
class Exercise
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
     * @var string
     *
     * @ORM\Column(name="exercise_name", type="string", length=100, nullable=false)
     */
    public $exerciseName;

    /**
     * @ORM\ManyToOne(targetEntity=Plan::class, inversedBy="exercise")
     */
    public $plan;

    /**
     * @ORM\OneToMany(targetEntity=ExerciseInstances::class, mappedBy="exercise")
     */
    public $exerciseItems;

    public function __construct()
    {
        $this->exerciseItems = new ArrayCollection();
    }

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * @return Collection|ExerciseInstances[]
     */
    public function getExerciseItems(): Collection
    {
        return $this->exerciseItems;
    }

    public function addExerciseItem(ExerciseInstances $exerciseItem): self
    {
        if (!$this->exerciseItems->contains($exerciseItem)) {
            $this->exerciseItems[] = $exerciseItem;
            $exerciseItem->setExercise($this);
        }

        return $this;
    }

    public function removeExerciseItem(ExerciseInstances $exerciseItem): self
    {
        if ($this->exerciseItems->removeElement($exerciseItem)) {
            // set the owning side to null (unless already changed)
            if ($exerciseItem->getExercise() === $this) {
                $exerciseItem->setExercise(null);
            }
        }

        return $this;
    }
}
