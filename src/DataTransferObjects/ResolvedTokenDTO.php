<?php

namespace Uniguide\Pportalen\DataTransferObjects;

class ResolvedTokenDTO
{
    /*
     * @var int
     */
    public $employee_num,ber;
    /*
     * @var string|null
     */
    public $ad_sid;
    /*
     * @var string|null
     */
    public $slack_id;
    /*
     * @var string|null
     */
    public $sling_id;
    /*
     * @var int|null
     */
    public $department_id;
    /*
     * @var string|null
     */
    public $ad_sam_account_name;
    /*
     * @var bool
     */
    public $is_administrator;
    /*
     * @var bool
     */
    public $is_developer;
    /*
     * @var string
     */
    public $full_name;
    /*
     * @var string
     */
    public $first_name;
    /*
     * @var string|null
     */
    public $last_name;
    /*
     * @var string|null
     */
    public $private_email;
    /*
     * @var string|null
     */
    public $private_phone;
    /*
     * @var string|null
     */
    public $work_phone;
    /*
     * @var string|nul
     */
    public $work_email;
    /*
     * @var string|null
     */
    public $birthday;
    /*
     * @var string|null
     */
    public $department_name;
    /*
     * @var int|null
     */
    public $years_old;

    public function __construct($args)
    {
        foreach ($args as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
