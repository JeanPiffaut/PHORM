<?php

use JetBrains\PhpStorm\Pure;
use ORM\iORM;

include_once dirname(__FILE__) . "/lib_iorm.php";

abstract class ORM implements iORM
{
    protected string $table;
    protected array  $columns;

    /**
     * Validate that the column sent by parameters exists in the table.
     * @param string $column
     * @return bool
     */
    public function ValidateColumn(string $column): bool
    {
        $columns = $this->getColumns();

        if($columns !== false) {

            if(in_array($column, $columns, true)) {

                return true;
            } else {

                return false;
            }
        } else {

            return false;
        }
    }

    /**
     * Returns the columns of the associated table. In case the columns of the table are not registered, this will
     * return false.
     * @return array|false
     */
    public function getColumns(): array|false
    {
        if(empty($this->columns)) {

            return false;
        } else {

            return $this->columns;
        }
    }
}
