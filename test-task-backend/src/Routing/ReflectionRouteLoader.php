<?php

namespace App\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Attribute\Route as RouteAttribute;
use ReflectionClass;
use ReflectionMethod;

class ReflectionRouteLoader
{
    public function load(string $controllerClass): RouteCollection
    {
        $collection = new RouteCollection();
        $class = new ReflectionClass($controllerClass);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $attributes = $method->getAttributes(RouteAttribute::class);

            foreach ($attributes as $attribute) {
                /** @var RouteAttribute $routeAttr */
                $routeAttr = $attribute->newInstance();

                // Construct the Route object from the Attribute
                // [FIX] Use public properties directly as getters are deprecated in Symfony 7.4+
                $route = new Route(
                    $routeAttr->path,
                    $routeAttr->defaults,
                    $routeAttr->requirements,
                    $routeAttr->options,
                    $routeAttr->host,
                    $routeAttr->schemes,
                    $routeAttr->methods,
                    $routeAttr->condition
                );

                // Set default controller to the class::method
                $route->setDefault('_controller', [$controllerClass, $method->getName()]);

                // Add to collection using the name from attribute, or auto-generate one
                $name = $routeAttr->getName() ?? strtolower(str_replace('\\', '_', $controllerClass) . '_' . $method->getName());

                $collection->add($name, $route);
            }
        }

        return $collection;
    }
}
