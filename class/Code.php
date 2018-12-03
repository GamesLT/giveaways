<?php

class mod_giveaways_Code
    extends icms_ipf_Object
{

    public function __construct($id = null, $data = array())
    {

        $this->initVar('code_id', self::DTYPE_INTEGER, 0, false);
        $this->initVar('type', self::DTYPE_INTEGER, '', false);
        $this->initVar('code', self::DTYPE_STRING, '', false);
        $this->initVar('claim_webbrowser', self::DTYPE_STRING, '', false);
        $this->initVar('claim_date', self::DTYPE_DATETIME, '', false);
        $this->initVar('is_claimed', self::DTYPE_BOOLEAN, false, false);
        $this->initVar('source_text', self::DTYPE_STRING, '', false);

        $this->setControl('type', array(
            'name' => 'select',
            'itemHandler' => 'type',
            'module' => 'giveaways'
        ));

        parent::__construct($id, $data);
    }

}