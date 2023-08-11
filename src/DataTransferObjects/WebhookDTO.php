<?php

namespace Uniguide\Pportalen\DataTransferObjects;

class WebhookDTO
{
    /*
     * @var string
     */
    public $application_id;
    /*
    * Possible values
    * UserCreated
    * UserRestored
    * UserMissing
    * @var string
    */
    public $event_name;
    /*
     * @var array
     */
    public $payload;

    public function __construct($args)
    {
        foreach ($args as $k => $v) {
            $this->{$k} = $v;
        }
    }
}
