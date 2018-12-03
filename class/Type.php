<?php

class mod_giveaways_Type extends icms_ipf_Object
{

    public function __construct($id = null, $data = array())
    {

        $this->initVar('type_id', self::DTYPE_INTEGER, 0, false);
        $this->initVar('name', self::DTYPE_STRING, '', false);
        $this->initVar('success_message', self::DTYPE_STRING, '', false);
        $this->initVar('login_text', self::DTYPE_STRING, '', false);
        $this->initVar('codes_left', self::DTYPE_INTEGER, 0, false);
        $this->initVar('codes_count', self::DTYPE_INTEGER, 0, false);

        $this->hideFieldFromForm('codes_left');
        $this->hideFieldFromForm('codes_count');

        parent::__construct($id, $data);
    }

}