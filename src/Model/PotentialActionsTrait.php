<?php

declare(strict_types=1);

/*
 * (c) 2019, Wesley O. Nichols
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wesnick\WorkflowBundle\Model;

use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Trait PotentialActionsTrait.
 *
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
trait PotentialActionsTrait
{
    #[ApiProperty(
        readable: true,
        writable: false,
        iri: "http://schema.org/potentialAction"
    )]
//    #[Groups(['workflowAction:output'])]
    private array $potentialAction = [];

    public function addPotentialAction(Action $action): void
    {
        $this->potentialAction[] = $action;
    }

    public function getPotentialAction(): array
    {
        return $this->potentialAction;
    }
}
