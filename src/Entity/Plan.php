<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Plan
 * @ApiResource()
 * @ORM\Table(name="plan")
 * @ORM\Entity
 */
class Plan
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
     * @ORM\Column(name="plan_name", type="string", length=150, nullable=false, options={"comment"="contains plan name"})
     */
    public $planName;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_description", type="text", length=65535, nullable=false, options={"comment"="contains plan description (optional)"})
     */
    public $planDescription;

    /**
     * @var int
     *
     * @ORM\Column(name="plan_difficulty", type="integer", nullable=false, options={"default"="1","comment"="1=beginner,2=intermediate,3=expert"})
     */
    public $planDifficulty = 1;

    /**
     * @ORM\OneToMany(targetEntity=Exercise::class, mappedBy="plan", cascade={"remove"})
     */
    public $exercise;

    /**
     * @ORM\OneToMany(targetEntity=PlanDays::class, mappedBy="plan", cascade={"remove"})
     */
    public $planDays;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="plan",  cascade={"remove"})
     */
    public $users;

    public function __construct()
    {
        $this->exercise = new ArrayCollection();
        $this->planDays = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return Collection|Exercise[]
     */
    public function getExercise(): Collection
    {
        return $this->exercise;
    }

    public function addExercise(Exercise $exercise): self
    {
        if (!$this->exercise->contains($exercise)) {
            $this->exercise[] = $exercise;
            $exercise->setPlan($this);
        }

        return $this;
    }

    public function removeExercise(Exercise $exercise): self
    {
        if ($this->exercise->removeElement($exercise)) {
            // set the owning side to null (unless already changed)
            if ($exercise->getPlan() === $this) {
                $exercise->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlanDays[]
     */
    public function getPlanDays(): Collection
    {
        return $this->planDays;
    }

    public function addPlanDay(PlanDays $planDay): self
    {
        if (!$this->planDays->contains($planDay)) {
            $this->planDays[] = $planDay;
            $planDay->setPlan($this);
        }

        return $this;
    }

    public function removePlanDay(PlanDays $planDay): self
    {
        if ($this->planDays->removeElement($planDay)) {
            // set the owning side to null (unless already changed)
            if ($planDay->getPlan() === $this) {
                $planDay->setPlan(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setPlan($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getPlan() === $this) {
                $user->setPlan(null);
            }
        }

        return $this;
    }


}
