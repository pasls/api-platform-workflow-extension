<?php

declare(strict_types=1);

/*
 * (c) 2019, Wesley O. Nichols
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wesnick\WorkflowBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Wesnick\WorkflowBundle\EventListener\SubjectValidatorListener;
use Wesnick\WorkflowBundle\EventListener\WorkflowOperationListener;
use Wesnick\WorkflowBundle\WorkflowActionGenerator;

/**
 * Class WorkflowPass.
 *
 * @author Wesley O. Nichols <spanishwes@gmail.com>
 */
class WorkflowPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('workflow.registry')) {
            return;
        }
        // @TODO: not sure if required to add Models to resource class directories.
//        $directories = $container->getParameter('api_platform.resource_class_directories');
//        $directories[] = realpath(__DIR__.'/../../Model');
//        $container->setParameter('api_platform.resource_class_directories', $directories);

        // @TODO: add validator
//        $validator        = $container->getDefinition(SubjectValidatorListener::class);

        $classMap = [];

        // Iterate over workflows and create services
        foreach ($this->workflowGenerator($container) as [$workflow, $supportStrategy]) {
            // only support InstanceOfSupportStrategy for now
            if (InstanceOfSupportStrategy::class !== $supportStrategy->getClass()) {
                throw new \RuntimeException(sprintf('Wesnick Workflow Bundle requires use of InstanceOfSupportStrategy, workflow %s is using strategy %s', (string) $workflow, $supportStrategy->getClass()));
            }

            $className = $supportStrategy->getArgument(0);
            $workflowShortName = $workflow->getArgument(3);
            $classMap[$className][] = $workflowShortName;

            // @TODO: add validator
//                $validator->addTag(
//                    'kernel.event_listener', ['event' => 'workflow.'.$workflow.'.guard', 'method' => 'onGuard']
//                );
        }

        $container->getDefinition(WorkflowActionGenerator::class)->setArgument('$enabledWorkflowMap', $classMap);
        $container->getDefinition(WorkflowOperationListener::class)->setArgument('$enabledWorkflowMap', $classMap);
    }

    private function workflowGenerator(ContainerBuilder $container): \Generator
    {
        $registry = $container->getDefinition('workflow.registry');
        foreach ($registry->getMethodCalls() as $call) {
            [, [$workflowReference, $supportStrategy]] = $call;
            yield [$container->getDefinition($workflowReference), $supportStrategy];
        }
    }
}
