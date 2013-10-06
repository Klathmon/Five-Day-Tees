<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Factory;

interface FactoryInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @param array $array
     *
     * @return object
     */
    public function create($array);

    /**
     * Deletes the object from the database
     *
     * @param object $object
     *
     */
    public function delete($object);

    /**
     * Persists an object to the database
     *
     * @param $object
     */
    public function persist($object);
}