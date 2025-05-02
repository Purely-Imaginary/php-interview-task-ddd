<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Lendable\Interview\Application\Service\OutputFormatter;
use Lendable\Interview\Domain\Event\FeeCalculatedEvent;
use Lendable\Interview\Domain\Repository\FeeStructureRepository;
use Lendable\Interview\Domain\Service\FeeCalculationStrategyInterface;
use Lendable\Interview\Domain\Service\InterpolationFeeStrategy;
use Lendable\Interview\Domain\Service\RoundingService;
use Lendable\Interview\Domain\Service\RoundingServiceInterface;
use Lendable\Interview\Infrastructure\Event\Listener\LogFeeCalculationListener;
use Lendable\Interview\Infrastructure\Formatting\StandardOutputFormatter;
use Lendable\Interview\Infrastructure\Persistence\InMemoryFeeStructureRepository;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use function DI\autowire;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    FeeStructureRepository::class => autowire(InMemoryFeeStructureRepository::class),
    RoundingServiceInterface::class => autowire(RoundingService::class),
    OutputFormatter::class => autowire(StandardOutputFormatter::class),

    // here we can change the fee calculation strategy to use a different one if needed
    FeeCalculationStrategyInterface::class => autowire(InterpolationFeeStrategy::class),

    // LOGGING
    LoggerInterface::class => function (ContainerInterface $c): LoggerInterface {
        $logPath = dirname(__DIR__) . '/var/log/app.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }

        $logger = new Logger('fee_calculator');
        $logger->pushHandler(new StreamHandler($logPath, Level::Debug));
        return $logger;
    },

    //EVENT DISPATCHER
    EventDispatcherInterface::class => autowire(EventDispatcher::class),
]);

try {
    $container = $containerBuilder->build();

    // EVENT DISPATCHER
    $dispatcher = $container->get(EventDispatcherInterface::class);
    $listener = $container->get(LogFeeCalculationListener::class);
    $dispatcher->addListener(FeeCalculatedEvent::class, $listener);

    return $container;
} catch (Exception $e) {
    fwrite(STDERR, "Error building container: " . $e->getMessage() . "\n");
    exit(1);
}