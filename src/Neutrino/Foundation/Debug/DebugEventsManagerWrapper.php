<?php

namespace Neutrino\Foundation\Debug;

use Neutrino\Debug\Exceptions\Helper;
use Phalcon\Events\Manager;
use Phalcon\Events\ManagerInterface;

/**
 * Class DebugEventsManagerWrapper
 *
 * @package App\Debug
 */
class DebugEventsManagerWrapper extends Manager implements ManagerInterface
{

    protected static $events;

    public static function getEvents()
    {
        return self::$events;
    }

    protected $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function fire($eventType, $source, $data = null, $cancelable = true, ...$args)
    {
        $eventParts = explode(':', $eventType, 2);
        self::$events[] = [
            'space' => $eventParts[0],
            'type' => $eventParts[1],
            'src' => Helper::verboseVar($source),
            'data' => !is_null($data) ? Helper::verboseVar($data) : null,
            'raw_data' => $data,
            'mt' => microtime(true),
        ];

        return $this->manager->fire($eventType, $source, $data, $cancelable, ...$args);
    }

    public function attach($eventType, $handler, $priority = 100, ...$args)
    {
        return $this->manager->attach($eventType, $handler, $priority, ...$args);
    }

    public function detach($eventType, $handler, ...$args)
    {
        return $this->manager->detach($eventType, $handler, ...$args);
    }

    public function detachAll($type = null, ...$args)
    {
        return $this->manager->detachAll($type, ...$args);
    }

    public function getListeners($type, ...$args)
    {
        return $this->manager->getListeners($type, ...$args);
    }

    public function enablePriorities($enablePriorities, ...$args)
    {
        return $this->manager->enablePriorities($enablePriorities, ...$args);
    }

    public function arePrioritiesEnabled(...$args)
    {
        return $this->manager->arePrioritiesEnabled(...$args);
    }

    public function collectResponses($collect, ...$args)
    {
        return $this->manager->collectResponses($collect, ...$args);
    }

    public function isCollecting(...$args)
    {
        return $this->manager->isCollecting(...$args);
    }

    public function getResponses(...$args)
    {
        return $this->manager->getResponses(...$args);
    }

    public function hasListeners($type, ...$args)
    {
        return $this->manager->hasListeners($type, ...$args);
    }

    public function __call($name, $arguments)
    {
        return $this->manager->$name(...$arguments);
    }
}
