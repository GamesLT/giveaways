<?php

class action_giveaways_GetLanguageConstants
    extends icms_action_base_Module
{

    protected $special_requirements = self::SR_NOTHING;

    public function exec(icms_action_Response &$response)
    {
        $constants = get_defined_constants(false);
        $tkey = '_AC_GIVEAWAYS_';
        $tl = strlen($tkey);
        $c2 = array();
        foreach (array_keys($constants) as $key) {
            if (substr($key, 0, $tl) == $tkey)
                $c2[strtolower(substr($key, $tl))] = $constants[$key];
        }

        $response->add('language_constants', $c2);
    }

}
