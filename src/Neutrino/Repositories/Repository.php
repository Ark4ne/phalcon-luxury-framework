<?php

namespace Neutrino\Repositories;

use Neutrino\Interfaces\Repositories\RepositoryInterface;
use Phalcon\Di\Injectable;

abstract class Repository extends Injectable implements RepositoryInterface
{
    /** @var \Neutrino\Model */
    protected $modelClass;

    /**
     * Repository constructor.
     *
     * @param null $modelClass
     *
     * @throws \RuntimeException
     */
    public function __construct($modelClass = null)
    {
        $this->modelClass = is_null($modelClass) ? $this->modelClass : $modelClass;

        if (empty($this->modelClass)) {
            throw new \RuntimeException(static::class . ' must have a $modelClass.');
        }
    }

    /**
     * @return \Phalcon\Mvc\Model\ResultsetInterface|\Neutrino\Model[]
     */
    public function all()
    {
        $class = $this->modelClass;

        return $class::find();
    }

    /**
     * @param null|array $criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function count(array $criteria = null)
    {
        $class = $this->modelClass;

        return $class::count($criteria);
    }

    /**
     * @param array|string|int $criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|\Neutrino\Model[]
     */
    public function find($criteria = null)
    {
        $class = $this->modelClass;

        return $class::find($criteria);
    }

    /**
     * @param array|string|int $criteria
     *
     * @return \Phalcon\Mvc\Model
     */
    public function first($criteria = null)
    {
        $class = $this->modelClass;

        return $class::findFirst($criteria);
    }

    /**
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function save($value)
    {
        return $this->transactionCall(is_array($value) ? $value : [$value], 'save');
    }

    /**
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function update($value)
    {
        return $this->transactionCall(is_array($value) ? $value : [$value], 'update');
    }

    /**
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function delete($value)
    {
        return $this->transactionCall(is_array($value) ? $value : [$value], 'delete');
    }

    /**
     * @return \Phalcon\Mvc\Model\Criteria
     */
    protected function query()
    {
        $class = $this->modelClass;

        return $class::query();
    }

    /**
     * @param \Neutrino\Model[] $values
     * @param string            $method
     *
     * @return bool
     */
    private function transactionCall(array $values, $method)
    {
        $this->db->begin();

        try {
            foreach ($values as $item) {
                $item->$method();
            }

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollback();

            return false;
        }
    }
}