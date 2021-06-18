<?php

use ORM\iORM;
use function DBFunctions\DBEscapeString;
use function DBFunctions\DBLastInsert;
use function DBFunctions\DBQuery;
use function DBFunctions\DBSetError;

include_once dirname(__FILE__) . "/lib_database.php";
include_once dirname(__FILE__) . "/lib_iorm.php";

abstract class ORM implements iORM
{
    protected string $table;
    protected array  $columns;

    /**
     * Validate that the column sent by parameters exists in the table.
     * @param string|array $column
     * @return bool
     */
    public function ValidateColumn(string|array $column): bool
    {
        $columns = $this->getColumns();

        if($columns !== false) {

            if(is_array($column) == true) {

                foreach ($column as $col => $value) {

                    if($this->ValidateColumn($value) === false) {

                        return false;
                    }
                }

                return true;
            } else {

                if(in_array($column, $columns, true)) {

                    return true;
                } else {

                    DBSetError("The column '" . $column . "' does not exist in table '" . $this->getTable() . "'.");
                    return false;
                }
            }
        } else {

            DBSetError("The columns of the table '" . $this->getTable() . "' have not been configured, in the ORM system");
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

    /**
     * It returns the columns separated by a comma or in case a "*" is sent, it will be returned.
     * @param array|string $columns
     * @return string
     */
    protected function setColumnsSeparated(array|string $columns): string
    {
        if($columns === "*") {

            return $columns;
        } else {

            if($this->ValidateColumn($columns) === true) {

                return implode(", ", $columns);
            } else {

                return false;
            }
        }
    }

    protected function setConditionColumns(array $condition, string $separation = "AND"): string|bool
    {
        $ret_condition = "";
        foreach ($condition as $column => $value) {

            if($this->ValidateColumn($column) === true) {

                if(empty($ret_condition)) {

                    $ret_condition .= $column . " = '" . $this->setColumn($column, DBEscapeString($value)) . "'";
                } else {

                    $ret_condition .= " " . $separation . " " . $column . " = '" . $this->setColumn($column, DBEscapeString($value)) . "'";
                }
            } else {

                $ret_condition = false;
                break;
            }
        }

        return $ret_condition;
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed  $value
     * @param string $type
     * @param int    $lenght
     * @return bool
     */
    protected function ValidateColumnValue(mixed $value, string $type, int $lenght): bool
    {
        if(gettype($value) == $type && mb_strlen($value) <= $lenght) {

            return true;
        } else {

            DBSetError("The value '" . $value . "', does not meet the conditions to be inserted in its column.");
            return false;
        }
    }

    public function Select(array|string $columns, ?array $conditions = null, string $order = null, string|int $limit = null): mysqli_result|false
    {
        $column = $this->setColumnsSeparated($columns);
        if($column === false) {

            return false;
        }

        $sql = "SELECT " . $column . " FROM " . $this->getTable();

        if($conditions !== null) {

            $condition = $this->setConditionColumns($conditions);

            if($condition === false) {

                return false;
            }

            $sql .= " WHERE " . $condition;
        }

        if($order !== null) {

            $sql .= " ORDER BY " . $order;
        }

        if($limit !== null) {

            $sql .= " LIMIT " . $limit;
        }

        $sql .= "; ";
        return DBQuery($sql);
    }

    /**
     * Insert the data sent into the database according to the table that executes the function.
     * @param array $columns
     * @return bool|int|string
     */
    public function Insert(array $columns): bool|int|string
    {
        $cols = "";
        $vals = "";

        foreach ($columns as $column => $value) {

            $value = $this->setColumn($column, DBEscapeString($value));

            if($this->ValidateColumn($column) && $value !== false) {

                if(empty($cols) && empty($vals)) {

                    $cols .= $column;
                    $vals .= "'" . $value . "'";
                } else {

                    $cols .= ", " . $column;
                    $vals .= ", '" . $value . "'";
                }
            } else {

                return false;
            }
        }

        $sql = "INSERT INTO " . $this->getTable() . "(" . $cols . ") VALUES (" . $vals . "); ";
        $result = DBQuery($sql);
        if($result === false) {

            return $result;
        } else {

            return DBLastInsert();
        }
    }

    /**
     * Updates the data of the current table and according to what is sent by parameters and with the conditions that
     * are defined and meet the conditions of the table.
     * @param array $columns
     * @param array|null $conditions
     * @return bool|mysqli_result
     */
    public function Update(array $columns, array|null $conditions = null): bool|mysqli_result
    {
        $column = $this->setConditionColumns($columns, ",");
        if($column === false) {

            return false;
        }

        $sql = "UPDATE " . $this->getTable() . " SET " . $column;

        if($conditions !== null) {

            $condition = $this->setConditionColumns($conditions);
            if($conditions === false) {

                return false;
            }
            $sql .= " WHERE " . $condition;
        }

        $sql .= "; ";

        return DBQuery($sql);
    }

    public function Delete(array|null $conditions = null)
    {
        $sql = "DELETE FROM " . $this->getTable();
        if($conditions !== null) {

            $condition = $this->setConditionColumns($conditions);
            if($conditions === false) {

                return false;
            }
            $sql .= " WHERE " . $condition;
        }

        $sql .= "; ";

        return DBQuery($sql);
    }
}
