<?php

class mod_giveaways_CodeHandler extends icms_ipf_Handler
{

    public function __construct(&$db)
    {
        parent::__construct($db, 'code', 'code_id', 'code', '', 'giveaways');
    }

    public function afterSave(&$obj)
    {
        $type_handler = icms_getModuleHandler('type', basename(dirname(dirname(__FILE__))), "giveaways");
        $tid = $obj->getVar('type');
        $type = $type_handler->get($tid);
        $call = $this->countAll($tid);
        $claimed = $this->countClaimed($tid);
        $type->setVar('codes_left', $call - $claimed);
        $type->setVar('codes_count', $call);
        return $type->store(true);
    }

    public function countAll($type_id)
    {
        $criteria = new icms_db_criteria_Compo();
        $criteria->add(new icms_db_criteria_Item('type', $type_id));
        return $this->getCount($criteria);
    }

    public function countClaimed($type_id)
    {
        $criteria = new icms_db_criteria_Compo();
        $criteria->add(new icms_db_criteria_Item('is_claimed', 1));
        $criteria->add(new icms_db_criteria_Item('type', $type_id));
        return $this->getCount($criteria);
    }

}