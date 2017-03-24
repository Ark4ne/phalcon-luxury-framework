<?php

namespace Neutrino\Interfaces\Repositories;

interface RepositoryInterface
{
    /**
     * Retourne tous les models d'une table.
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|\Neutrino\Model[]
     */
    public function all();

    /**
     * @param null|array $criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function count(array $criteria = null);

    /**
     * Recherche & renvoie une liste de model selon les criteria transmis
     *
     * @param null|string|array $criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|\Neutrino\Model[]
     */
    public function find($criteria = null);

    /**
     * Recherche & renvoie le premier model selon les criteria transmis
     *
     * @param null|string|array $criteria
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface|\Neutrino\Model[]
     */
    public function first($criteria = null);

    /**
     * Appel la methode save sur le, ou les, models transmis, dans une transaction.
     *
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function save($value);

    /**
     * Appel la methode update sur le, ou les, models transmis, dans une transaction.
     *
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function update($value);

    /**
     * Appel la methode delete sur le, ou les, models transmis, dans une transaction.
     *
     * @param \Neutrino\Model|\Neutrino\Model[] $value
     *
     * @return bool
     */
    public function delete($value);
}