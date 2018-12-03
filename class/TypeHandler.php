<?php

class mod_giveaways_TypeHandler extends icms_ipf_Handler
{

    public function __construct(&$db)
    {
        parent::__construct($db, 'type', 'type_id', 'name', '', 'giveaways');
    }

}