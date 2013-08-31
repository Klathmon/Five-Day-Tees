<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

namespace Mapper;


use Entity\Entity;

interface Mapper
{
    public function getByID($ID);

    public function listAll($start = 0, $stop = null);

    public function delete(Entity $entity);

    public function persist(Entity $entity);
}