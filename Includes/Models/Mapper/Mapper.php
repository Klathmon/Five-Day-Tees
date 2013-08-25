<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

namespace Mapper;

use PDO;

interface Mapper
{
    public function getByID($ID);

    public function listAll($start = 0, $stop = null);

    public function delete($entity);

    public function persist($entity);
}