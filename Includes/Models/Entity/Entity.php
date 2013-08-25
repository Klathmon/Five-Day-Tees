<?php
/**
 * Created by: Gregory Benner.
 * Date: 8/21/13
 */

namespace Entity;

/**
 * Interface Entity
 *
 * Entities need to use these functions at a minimum.
 */
interface Entity
{
    /**
     * The constructor is called blank for "new" objects, and called with an $ID to setup an object from the database
     *
     * @param int $ID The id of the new object
     */
    public function __construct($ID = null);
}