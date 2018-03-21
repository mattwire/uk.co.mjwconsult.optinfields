<?php

require_once 'optinfields.civix.php';
use CRM_Optinfields_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function optinfields_civicrm_config(&$config) {
  _optinfields_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function optinfields_civicrm_xmlMenu(&$files) {
  _optinfields_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function optinfields_civicrm_install() {
  _optinfields_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function optinfields_civicrm_postInstall() {
  _optinfields_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function optinfields_civicrm_uninstall() {
  _optinfields_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function optinfields_civicrm_enable() {
  _optinfields_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function optinfields_civicrm_disable() {
  _optinfields_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function optinfields_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _optinfields_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function optinfields_civicrm_managed(&$entities) {
  _optinfields_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function optinfields_civicrm_caseTypes(&$caseTypes) {
  _optinfields_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function optinfields_civicrm_angularModules(&$angularModules) {
  _optinfields_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function optinfields_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _optinfields_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_entityTypes
 */
function optinfields_civicrm_entityTypes(&$entityTypes) {
  _optinfields_civix_civicrm_entityTypes($entityTypes);
}

function optinfields_civicrm_custom($op, $groupID, $entityID, &$params) {
  $custom_group = civicrm_api3('CustomGroup', 'getsingle', array('id' => $groupID));
  if ($custom_group['extends'] !== 'Contact') {
    return;
  }
  $contact = civicrm_api3('Contact', 'getsingle', array('id' => $entityID));

  $changed = FALSE;
  foreach($params as $field) {
    if (!empty($field['custom_field_id'])) {
      $endOfName = strpos($field['column_name'], '_');
      if ($endOfName !== FALSE) {
        $privacyOption = substr($field['column_name'], 0, $endOfName);
        if ($field['value'] == '') {
          continue;
        }
        $newValue = empty($field['value']) ? '1' : '0';
        if ($privacyOption == 'phone') {
          // Special case to handle sms (as we're treating it as the same "phone" permission
          if ($contact['do_not_sms'] !== $newValue) {
            $contact['do_not_sms'] = $newValue;
            $changed = TRUE;
          }
        }
        if ($contact['do_not_'.$privacyOption] !== $newValue) {
          $contact['do_not_' . $privacyOption] = $newValue;
          $changed = TRUE;
        }
      }
    }
  }
  if (!$changed) {
    return;
  }

  try {
    civicrm_api3('Contact', 'create', $contact);
  }
  catch (CiviCRM_API3_Exception $ex) {
    throw new Exception('Could not update contact with privacy options in '.__METHOD__
      .', contact your system administrator. Error from API Contact create: '.$ex->getMessage());
  }
}
