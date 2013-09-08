<?php
/**
 * Created by: Gregory Benner.
 * Date: 9/8/13
 */

namespace Mapper;

interface MapperInterface
{

    /**
     * Create a new object. This object will not exist in the database until persisted
     *
     * @return object
     */
    public function create();
    
    /**
     * Deletes the object from the database
     * 
     * @param object $object
     *
     */
    public function delete($object);

    /**
     * Saves an object to the database in it's entirety
     * *Note* this method should also unset() the entity so no further changes can be made
     * 
     * @param object $object
     *
     */
    public function persist($object);
}