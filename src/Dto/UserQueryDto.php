<?php

declare(strict_types=1);

namespace Siganushka\UserBundle\Dto;

use Doctrine\Common\Collections\Expr\Comparison;
use Siganushka\GenericBundle\Attribute\QueryFilter;
use Siganushka\GenericBundle\Dto\DateRangeDto;

class UserQueryDto
{
    public function __construct(
        #[QueryFilter(expr: Comparison::CONTAINS)]
        public readonly ?string $identifier = null,
        #[QueryFilter]
        public readonly ?bool $enabled = null,
        #[QueryFilter(expr: ['startAt' => Comparison::GTE, 'endAt' => Comparison::LTE])]
        public readonly ?DateRangeDto $createdAt = null,
    ) {
    }
}
