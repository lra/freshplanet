<?php


class HiscoreTable extends Doctrine_Table
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Hiscore');
    }
}