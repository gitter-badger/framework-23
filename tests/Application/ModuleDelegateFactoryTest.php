<?php


namespace Tests\Application;


use Interop\Container\ContainerInterface;
use Interop\Http\Middleware\DelegateInterface;
use Onion\Framework\Application\Factory\ModuleDelegateFactory;
use Tests\Application\Stubs\MiddlewareStub;
use Zend\Diactoros\Response\EmitterInterface;

class ModuleDelegateFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testModuleBuilding()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(EmitterInterface::class)->willReturn(
            $this->prophesize(EmitterInterface::class)->reveal()
        );
        $container->get(MiddlewareStub::class)->willReturn(new MiddlewareStub());
        $container->get(Stubs\SimpleModuleStub::class)->willReturn(new Stubs\SimpleModuleStub());
        $container->get('middleware')->willReturn([Stubs\MiddlewareStub::class]);
        $container->has('modules')->willReturn(true);
        $container->get('modules')->willReturn([
            '/' => Stubs\SimpleModuleStub::class
        ]);

        $factory = new ModuleDelegateFactory();
        $this->assertInstanceOf(DelegateInterface::class, $factory->build($container->reveal()));
    }

    public function testExceptionWhenNotImplementingInterface()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(MiddlewareStub::class)->willReturn(new MiddlewareStub());
        $container->get(\stdClass::class)->willReturn(new \stdClass());
        $container->get(Stubs\SimpleModuleStub::class)->willReturn(new Stubs\SimpleModuleStub());
        $container->get('middleware')->willReturn([MiddlewareStub::class, 'modules']);
        $container->has('modules')->willReturn(true);
        $container->get('modules')->willReturn([
            \stdClass::class
        ]);

        $factory = new ModuleDelegateFactory();
        $this->expectException(\AssertionError::class);
        $factory->build($container->reveal());
    }

    public function testReturningDelegateWhenBuildingWithModules()
    {
        $stub = $this->prophesize(Subs\MiddlewareStub::class);
        $stub->willImplement(\Onion\Framework\Application\Interfaces\ModuleInterface::class);
        $stub->build(new \Prophecy\Argument\Token\AnyValueToken())
            ->willReturn($this->prophesize(\Onion\Framework\Application\Application::class)
            ->reveal()
        );

        $container = $this->prophesize(ContainerInterface::class);
        $container->get(Stubs\MiddlewareStub::class)->willReturn($stub->reveal());
        $container->get('middleware')->willReturn(['modules']);
        $container->has('modules')->willReturn(true);
        $container->get('modules')->willReturn([Stubs\MiddlewareStub::class]);

        $factory = new ModuleDelegateFactory();
        $this->assertInstanceOf(DelegateInterface::class, $factory->build($container->reveal()));
    }
}