<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * PlanDays
 * @ApiResource()
 * @ORM\Table(name="plan_days")
 * @ORM\Entity
 */
class PlanDays
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
     * @ORM\Column(name="day_name", type="string", length=100, nullable=false, options={"comment"="name for this day, optional"})
     */
    public $dayName = '';

    /**
     * @var int
     *
     * @ORM\Column(name="in_order", type="integer", nullable=false)
     */
    public $order = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Plan::class, inversedBy="planDays")
     */
    public $plan;

    public function getPlan(): ?Plan
    {
        return $this->plan;
    }

    public function setPlan(?Plan $plan): self
    {
        $this->plan = $plan;

        return $this;
    }

}
