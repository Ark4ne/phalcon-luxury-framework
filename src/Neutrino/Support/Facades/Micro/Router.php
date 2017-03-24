<?php

namespace Neutrino\Support\Facades\Micro;

use Neutrino\Constants\Services;
use Neutrino\Support\Facades\Facade;

/**
 * Class Route
 *
 *  @package Neutrino\Support\Facades
 *
 * @method static \Phalcon\Cli\Router\RouteInterface addTask(string $pattern, mixed $paths, mixed $httpMethods = null, mixed $position = null) Adds a route to the router (CLI Only)
 *
 * @method static string getRewriteUri() Get rewrite info. This info is read from $_GET['_url']. This returns '/' if the rewrite information cannot be read
 * @method static \Phalcon\Mvc\RouterInterface setUriSource(mixed $uriSource) Sets the URI source. One of the URI_SOURCE_constants
 * @method static \Phalcon\Mvc\RouterInterface removeExtraSlashes(bool $remove) Set whether router must remove the extra slashes in the handled routes
 * @method static \Phalcon\Mvc\RouterInterface setDefaultNamespace(string $namespaceName) Sets the name of the default namespace
 * @method static \Phalcon\Mvc\RouterInterface setDefaultModule(string $moduleName) Sets the name of the default module
 * @method static \Phalcon\Mvc\RouterInterface setDefaultController(string $controllerName) Sets the default controller name
 * @method static \Phalcon\Mvc\RouterInterface setDefaultAction(string $actionName) Sets the default action name
 * @method static \Phalcon\Mvc\RouterInterface setDefaults(array $defaults) Sets an array of default paths. If a route is missing a path the router will use the defined here
 * @method static array getDefaults() Returns an array of default parameters
 * @method static void handle(string $uri) Handles routing information received from the rewrite engine
 * @method static \Phalcon\Mvc\Router\RouteInterface add(string $pattern, mixed $paths, mixed $httpMethods = null, mixed $position = null) Adds a route to the router without any HTTP constraint
 * @method static \Phalcon\Mvc\Router\RouteInterface addGet(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is GET
 * @method static \Phalcon\Mvc\Router\RouteInterface addPost(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is POST
 * @method static \Phalcon\Mvc\Router\RouteInterface addPut(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is PUT
 * @method static \Phalcon\Mvc\Router\RouteInterface addPatch(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is PATCH
 * @method static \Phalcon\Mvc\Router\RouteInterface addDelete(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is DELETE
 * @method static \Phalcon\Mvc\Router\RouteInterface addOptions(string $pattern, mixed $paths, mixed $position = null) Add a route to the router that only match if the HTTP method is OPTIONS
 * @method static \Phalcon\Mvc\Router\RouteInterface addHead(string $pattern, mixed $paths, mixed $position = null) Adds a route to the router that only match if the HTTP method is HEAD
 * @method static \Phalcon\Mvc\RouterInterface mount(mixed $group) Mounts a group of routes in the router
 * @method static \Phalcon\Mvc\RouterInterface notFound(mixed $paths) Set a group of paths to be returned when none of the defined routes are matched
 * @method static void clear() Removes all the pre-defined routes
 * @method static string getNamespaceName() Returns the processed namespace name
 * @method static string getModuleName() Returns the processed module name
 * @method static string getControllerName() Returns the processed controller name
 * @method static string getActionName() Returns the processed action name
 * @method static array getParams() Returns the processed parameters
 * @method static \Phalcon\Mvc\Router\RouteInterface getMatchedRoute() Returns the route that matchs the handled URI
 * @method static array getMatches() Returns the sub expressions in the regular expression matched
 * @method static bool wasMatched() Checks if the router macthes any of the defined routes
 * @method static \Phalcon\Mvc\Router\RouteInterface getRoutes() Returns all the routes defined in the router
 * @method static \Phalcon\Mvc\Router\RouteInterface|bool getRouteById(mixed $id) Returns a route object by its id
 * @method static \Phalcon\Mvc\Router\RouteInterface|bool getRouteByName(string $name) Returns a route object by its name
 * @method static bool isExactControllerName() Returns whether controller name should not be mangled
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Services::MICRO_ROUTER;
    }
}
