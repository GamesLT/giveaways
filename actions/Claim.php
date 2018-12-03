<?php

class action_giveaways_Claim
    extends icms_action_base_Module
{

    protected $special_requirements = self::SR_NOTHING;

    public function __construct($params = array())
    {
        $this->initVar('source_page', self::DTYPE_ARRAY, array(), false);
        $this->initVar('type', self::DTYPE_INTEGER, '', false);

        parent::__construct($params);
    }

    public function exec(icms_action_Response &$response)
    {

        if (!($type = $this->getType()))
            return $response->error(_AC_GIVEAWAYS_ERROR_TYPE_NOT_FOUND);

        if (!$this->isValid($type))
            return $response->error(_AC_GIVEAWAYS_ERROR_NOT_LOGGED_IN);

        if (!($code = $this->findCode()))
            return $response->error(_AC_GIVEAWAYS_ERROR_NOT_CODES_LEFT);

        $this->updateCodeInfo($code);
        $response->msg(sprintf($type->getVar('success_message'), $code->getVar('code'), $type->getVar('codes_left'), $type->getVar('codes_count')));
        $response->add('is_claimed', true);
        $response->add('claimer', $this->source_page);

        // $response->addModuleAction('GetInfo', array('type' => $this->type), 'giveaways');
    }

    /**
     *
     * @return mod_giveaways_Type
     */
    public function getType()
    {
        $type_handler = icms_getModuleHandler('type', 'giveaways');
        $type = $type_handler->get($this->type);
        if ($type->isNew())
            return null;
        return $type;
    }

    /**
     *
     * @param mod_giveaways_Type $type
     * @return boolean
     */
    public function isValid(mod_giveaways_Type &$type)
    {

        $login_text = $type->getVar('login_text');
        if (empty($login_text))
            return true;

        $pmatch = '~' . $login_text . '~i';
        $ret = false;
        foreach ($this->source_page as $item) {
            $ret = $ret || (preg_match($pmatch, $item, $matches) > 0);
        }

        return $ret;
    }

    /**
     *
     * @return mod_giveaways_Code
     */
    public function findCode()
    {
        $code_handler = icms_getModuleHandler('code', 'giveaways');
        $criteria = new icms_db_criteria_Compo();
        $criteria->add(new icms_db_criteria_Item('is_claimed', 0));
        $criteria->add(new icms_db_criteria_Item('type', $this->type));
        $criteria->setStart(0);
        $criteria->setLimit(1);

        $object = $code_handler->getObjects($criteria);

        return isset($object[0]) ? $object[0] : null;
    }

    /**
     *
     * @param mod_giveaways_Code $object
     */
    public function updateCodeInfo(mod_giveaways_Code &$object)
    {
        $object->setVar('claim_webbrowser', $_SERVER['HTTP_USER_AGENT']);
        $object->setVar('claim_date', time());
        $object->setVar('is_claimed', 1);
        $object->setVar('source_text', json_encode($this->source_page));
        $object->store();
    }

}
