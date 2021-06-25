<?php

namespace ORM;

interface iORM
{
    /**
     * According to the name of the column sent by parameter, this function is configured using its function to set and
     * obtain the value.These functions will be in charge of independently validating, configuring and returning the
     * values as needed by the table.
     * @param string $column
     * @param mixed  $value
     * @return mixed
     */
    public function setColumn(mixed $column, mixed $value): mixed;
}
