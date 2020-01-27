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

  // For Wordpress we need to register hook_civicrm_custom for it to fire from frontend forms (eg. Caldera CiviCRM integration)
  if (!function_exists('civi_wp') || !function_exists('add_filter')) return;
  add_filter('civicrm_custom', 'optinfields_civicrm_custom', 10, 4);
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
  $customGroup = civicrm_api3('CustomGroup', 'getsingle', array('id' => $groupID));
  if (($customGroup['name'] !== 'Communication_Preferences') || ($customGroup['extends'] !== 'Contact')) {
    return;
  }
  // Get a list of custom fields for the group
  $customFields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $groupID));
  // Get a list of field column names and set value to "is_active" status
  // We use this to check the SMS/Phone is a separate permission or not
  foreach ($customFields['values'] as $customField) {
    $fieldsByColumnName[$customField['column_name']] = $customField['is_active'];
  }
  // Get full details for the contact record
  $contact = civicrm_api3('Contact', 'getsingle', array('id' => $entityID));

  $changed = FALSE;
  foreach($params as $field) {
    if (!empty($field['custom_field_id'])) {
      if (!in_array($field['column_name'], array_keys($fieldsByColumnName))) {
        // Only interested in our optinfields.
        return;
      }
      // Column names are in format phone_XX - we map the first part to the respective privacy option on the contact record
      $endOfName = strpos($field['column_name'], '_');
      if ($endOfName !== FALSE) {
        $privacyOption = substr($field['column_name'], 0, $endOfName);
        if ($field['value'] == '') {
          continue;
        }
        // Empty or No = Do Not XX
        if (empty($field['value']) || (strtolower($field['value']) === 'no')) {
          $newValue = 1;
        }
        else {
          $newValue = 0;
        }

        if ($privacyOption == 'phone') {
          // If the sms custom field is disabled, we set the sms privacy option to mirror the phone privacy option.
          if (!in_array('sms_43', array_keys($fieldsByColumnName))) {
            // Special case to handle sms (as we're treating it as the same "phone" permission
            if ($contact['do_not_sms'] !== $newValue) {
              $contact['do_not_sms'] = $newValue;
              $changed = TRUE;
            }
          }
        }
        // Only update the contact record if the value has actually changed.
        if (isset($contact['do_not_'.$privacyOption]) && $contact['do_not_'.$privacyOption] !== $newValue) {
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
