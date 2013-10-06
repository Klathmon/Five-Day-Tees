<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Abstracts;

abstract class Entity
{
    final private function __construct(){ }

    public function getID()
    {

        $fullClassName = explode('\\', get_class($this));
        end($fullClassName);

        return $this->{strtolower(prev($fullClassName)) . 'ID'};
    }
}