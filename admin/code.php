<?php

/**
 * Edit a Content
 *
 * @param int $code_id Contentid to be edited
 */
function editcontent(&$code_handler, &$icmsAdminTpl, $code_id = 0, $clone = false, $code_pid = false)
{

    $code = $code_handler->get($code_id);

    if (!$clone && !$code->isNew()) {
        $sform = $code->getForm(_EDIT, 'additem');
        $sform->assign($icmsAdminTpl);
        $icmsAdminTpl->display('db:giveaways_admin_code.html');
    } else {
        $sform = $code->getForm(_ADD, 'additem');
        $sform->assign($icmsAdminTpl);
        $icmsAdminTpl->display('db:giveaways_admin_code.html');
    }
}

include_once "../include/common.php";

$code_handler = icms_getModuleHandler('code', basename(dirname(dirname(__FILE__))), "giveaways");

$clean_id = isset($_REQUEST['code_id']) ? (int)$_REQUEST['code_id'] : 0;

switch ($_REQUEST['op']) {
    case "clone" :
        icms_cp_header();
        editcontent($code_handler, $icmsAdminTpl, $clean_id, true);
        break;

    case "mod" :
        icms_cp_header();
        editcontent($code_handler, $icmsAdminTpl, $clean_id, false);
        break;

    case "additem" :
        $controller = new icms_ipf_Controller($code_handler);
        $controller->storeFromDefaultForm(_AM_CONTENT_CONTENT_CREATED, _AM_CONTENT_CONTENT_MODIFIED);

        break;

    case "del" :
        $controller = new icms_ipf_Controller($code_handler);
        $controller->handleObjectDeletion();

        break;

    case 'import':
        $codes = explode("\n", $_REQUEST['codes']);
        foreach ($codes as $code) {
            $codeObj = $code_handler->create();
            $codeObj->setVar('code', $code);
            $codeObj->setVar('type', $_REQUEST['type_id']);
            $codeObj->store();
        }
        global $impresscms;
        redirect_header($impresscms->urls['previouspage'], 3, _AM_CONTENT_CONTENT_CREATED);
        break;

    default:

        icms_cp_header();

        icms::$module->displayAdminMenu(1, _MI_GIVEAWAYS_CODE);

        $objectTable = new icms_ipf_view_Table($code_handler);
        $objectTable->addColumn(new icms_ipf_view_Column('code', false, false));
        $objectTable->addColumn(new icms_ipf_view_Column('claim_webbrowser', 'center', 100));
        $objectTable->addColumn(new icms_ipf_view_Column('claim_date', 'center', 100));

        $objectTable->addIntroButton('additem', 'code.php?op=additem&type_id=' . $_GET['type_id'], _ADD);

        $icmsAdminTpl->assign('content_table', $objectTable->fetch());

        $icmsAdminTpl->display('db:giveaways_admin_code.html');
        break;
}
icms_cp_footer();