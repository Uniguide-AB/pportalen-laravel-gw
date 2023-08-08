<?php

namespace Uniguide\Pportalen\DataTransferObjects;

class DepartmentDTO
{
    /*
     * @var int
     */
    public $id;
    /*
     * @var string
     */
    public $name;
    public function __construct($args)
    {
        foreach ($args as $k => $v) {
            $this->{$k} = $v;
        }
    }
}