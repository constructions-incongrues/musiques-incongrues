<?php

/**
 * LUM_UserTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class LUM_UserTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object LUM_UserTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('LUM_User');
    }
}