<?php

class action_giveaways_GetInfo
    extends icms_action_base_Module
{

    protected $special_requirements = self::SR_NOTHING;

    public function __construct($params = array())
    {
        $this->initVar('type', self::DTYPE_INTEGER, 0, false);

        parent::__construct($params);
    }

    public function exec(icms_action_Response &$response)
    {
        $type_handler = icms_getModuleHandler('type', 'giveaways');
        $type = $type_handler->get($this->type);
        if ($type->isNew())
            return $response->error(_AC_GIVEAWAYS_ERROR_TYPE_NOT_FOUND);

        $response->add('giveaway', $type->toArray());
    }

}
