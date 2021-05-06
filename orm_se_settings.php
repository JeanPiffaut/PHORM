<?php

include_once dirname(__FILE__) . "/lib_orm.php";


class se_settings extends ORM
{
    public string $table = "se_settings";
    public array  $columns = array("sese_id", "sese_description", "sese_value");
    private string $sese_id;
    private string $sese_description;
    private string $sese_value;

    /**
     * @inheritDoc
     */
    public function setColumn(string $column, mixed $value): mixed
    {
        if($this->ValidateColumn($column) === true) {

            switch ($column){
                case "sese_id":

                    if($this->setSeseId($value)) {

                        $col_value = $this->getSeseId();
                    } else {

                        return false;
                    }
                    break;
                case "sese_description":

                    if($this->setSeseDescription($value)) {

                        $col_value = $this->getSeseDescription();
                    } else {

                        return false;
                    }
                    break;
                case "sese_value":

                    $this->setSeseValue($value);
                    $col_value = $this->getSeseValue();
                    break;
                default:

                    $col_value = $value;
                    break;
            }

            return $col_value;
        } else {

            return false;
        }
    }

    /**
     * @return string
     */
    public function getSeseId(): string
    {
        return $this->sese_id;
    }

    /**
     * @param string $sese_id
     * @return bool
     */
    public function setSeseId(string $sese_id): bool
    {
        if($this->ValidateColumnValue($sese_id, "string", 255)) {

            $this->sese_id = $sese_id;
            return true;
        } else {

            return false;
        }
    }

    /**
     * @return string
     */
    public function getSeseDescription(): string
    {
        return $this->sese_description;
    }

    /**
     * @param string $sese_description
     */
    public function setSeseDescription(string $sese_description): bool
    {
        if($this->ValidateColumnValue($sese_description, "string", 255)) {

            $this->sese_description = $sese_description;
        } else {

            return false;
        }
    }

    /**
     * @return string
     */
    public function getSeseValue(): string
    {
        return $this->sese_value;
    }

    /**
     * @param string $sese_value
     */
    public function setSeseValue(string $sese_value): void
    {
        $this->sese_value = $sese_value;
    }
}
