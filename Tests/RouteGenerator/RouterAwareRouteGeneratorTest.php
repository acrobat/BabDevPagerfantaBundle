<?php declare(strict_types=1);

namespace BabDev\PagerfantaBundle\Tests\RouteGenerator;

use BabDev\PagerfantaBundle\RouteGenerator\RouterAwareRouteGenerator;
use Pagerfanta\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class RouterAwareRouteGeneratorTest extends TestCase
{
    private function createRouter(): UrlGeneratorInterface
    {
        $routeCollection = new RouteCollection();
        $routeCollection->add('pagerfanta_view', new Route('/pagerfanta-view'));

        return new UrlGenerator($routeCollection, new RequestContext());
    }

    public function testARouteIsGeneratedWithEmptyOptions(): void
    {
        $generator = new RouterAwareRouteGenerator($this->createRouter(), ['routeName' => 'pagerfanta_view']);

        $this->assertSame('/pagerfanta-view?page=1', $generator(1));
    }

    public function testARouteIsGeneratedWithFirstPageOmitted(): void
    {
        $generator = new RouterAwareRouteGenerator(
            $this->createRouter(),
            ['routeName' => 'pagerfanta_view', 'omitFirstPage' => true]
        );

        $this->assertSame('/pagerfanta-view', $generator(1));
    }

    public function testARouteIsGeneratedWithACustomPageParameter(): void
    {
        $generator = new RouterAwareRouteGenerator(
            $this->createRouter(),
            ['routeName' => 'pagerfanta_view', 'pageParameter' => '[custom_page]']
        );

        $this->assertSame('/pagerfanta-view?custom_page=1', $generator(1));
    }

    public function testARouteIsGeneratedWithAdditionalParameters(): void
    {
        $generator = new RouterAwareRouteGenerator(
            $this->createRouter(),
            ['routeName' => 'pagerfanta_view', 'routeParams' => ['hello' => 'world']]
        );

        $this->assertSame('/pagerfanta-view?hello=world&page=1', $generator(1));
    }

    public function testARouteIsNotGeneratedWhenTheRouteNameParameterIsMissing(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $generator = new RouterAwareRouteGenerator(
            $this->createRouter(),
            ['routeParams' => ['hello' => 'world']]
        );

        $generator(1);
    }
}
