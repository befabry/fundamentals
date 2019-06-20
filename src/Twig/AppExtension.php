<?php

namespace App\Twig;

use App\Service\MarkdownHelper;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * AppExtension constructor.
     * @param ContainerInterface $container
     *
     * ContainerInterface is a mini container (~local) that holds a subset of services
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('cached_markdown', [$this, 'processMarkdown'], ['is_safe' => ['html']]),
        ];
    }

//    public function getFunctions(): array
//    {
//        return [
//            new TwigFunction('function_name', [$this, 'doSomething']),
//        ];
//    }

    public function processMarkdown($value)
    {
        return $this->container
            ->get(MarkdownHelper::class)
            ->parse($value);
    }

    /**
     * @return array The required service types, optionally keyed by service names
     *
     * Autowire to the class container to make it accessible directly
     */
    public static function getSubscribedServices()
    {
        return [
            MarkdownHelper::class,
        ];
    }
}
