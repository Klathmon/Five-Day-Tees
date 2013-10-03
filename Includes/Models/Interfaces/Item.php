<?php
/**
 * Created by: Gregory Benner.
 * Date: 10/3/13
 */

namespace Interfaces;

interface Item
{
    public function getIDFromParts($array);
    public function getPartsFromID($ID);
}