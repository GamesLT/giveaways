<?php

/**
 * Admin page to manage contents
 *
 * List, add, edit and delete content objects
 *
 * @copyright    The ImpressCMS Project
 * @license        http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @since        1.0
 * @author        Rodrigo P Lima aka TheRplima <therplima@impresscms.org>
 * @package        content
 * @version        $Id: content.php 21969 2011-07-04 18:41:00Z mcdonald3072 $
 */

/**
 * Edit a Content
 *
 * @param int $type_id Contentid to be edited
 */
function editcontent(&$type_handler, &$icmsAdminTpl, $type_id = 0, $clone = false, $type_pid = false)
{

    $type = $type_handler->get($type_id);

    if (!$clone && !$type->isNew()) {
        $sform = $type->getForm(_EDIT, 'additem');
        $sform->assign($icmsAdminTpl);
        $icmsAdminTpl->display('db:giveaways_admin_type.html');
    } else {
        $sform = $type->getForm(_ADD, 'additem');
        $sform->assign($icmsAdminTpl);
        $icmsAdminTpl->display('db:giveaways_admin_type.html');
    }
}

include_once "../include/common.php";

$type_handler = icms_getModuleHandler('type', "giveaways");

$clean_id = isset($_REQUEST['type_id']) ? (int)$_REQUEST['type_id'] : 0;

switch ($_REQUEST['op']) {
    case "clone" :
        icms_cp_header();
        editcontent($type_handler, $icmsAdminTpl, $clean_id, true);
        break;

    case "mod" :
        icms_cp_header();
        editcontent($type_handler, $icmsAdminTpl, $clean_id, false);
        break;

    case "additem" :
        $controller = new icms_ipf_Controller($type_handler);
        $controller->storeFromDefaultForm(_AM_CONTENT_CONTENT_CREATED, _AM_CONTENT_CONTENT_MODIFIED);

        break;

    case "del" :
        $controller = new icms_ipf_Controller($type_handler);
        $controller->handleObjectDeletion();

        break;

    default:

        if (isset($_GET['type_id']) && $_GET['type_id'] > 0) {
            header('Location: ./code.php?type_id=' . $_GET['type_id']);
            break;
        }

        icms_cp_header();

        icms::$module->displayAdminMenu(1, _MI_GIVEAWAYS_TYPE);

        $objectTable = new icms_ipf_view_Table($type_handler);
        $objectTable->addColumn(new icms_ipf_view_Column('name', false, false));
        $objectTable->addColumn(new icms_ipf_view_Column('codes_left', 'center', 100));
        $objectTable->addColumn(new icms_ipf_view_Column('codes_count', 'center', 100));
        $objectTable->addIntroButton('additem', 'type.php?op=mod', _ADD);

        $icmsAdminTpl->assign('content_table', $objectTable->fetch());

        $icmsAdminTpl->display('db:giveaways_admin_type.html');
        break;
}
icms_cp_footer();