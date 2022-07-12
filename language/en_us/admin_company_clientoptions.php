<?php
/**
 * Language definitions for the Admin Company Client Options controller/views
 *
 * @package blesta
 * @subpackage blesta.language.en_us
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */

// Success messages
$lang['AdminCompanyClientOptions.!success.field_updated'] = 'The client custom field has been successfully updated.';
$lang['AdminCompanyClientOptions.!success.field_created'] = 'The client custom field has been successfully created.';
$lang['AdminCompanyClientOptions.!success.field_deleted'] = 'The client custom field has been successfully deleted.';
$lang['AdminCompanyClientOptions.!success.requiredfields_updated'] = 'The required fields have been successfully updated.';
$lang['AdminCompanyClientOptions.!success.general_updated'] = 'The general settings have been successfully updated.';

// Notices
$lang['AdminCompanyClientOptions.!notice.group_settings'] = 'NOTE: These settings only apply to Client Groups that inherit their settings from the Company.';

// Tooltips
$lang['AdminCompanyClientOptions.!tooltip.state'] = 'Use caution when requiring a State/Province be selected. Some countries do not have any states. Clients in those countries would be unable to save their contact details. We recommend not requiring this field.';
$lang['AdminCompanyClientOptions.!tooltip.unique_contact_emails'] = 'Restricts email addresses for contacts. Primary Contacts means no two primary contacts (i.e. clients) can have the same email address. All Contacts means no contacts of any type can have the same email address as another contact.';

$lang['AdminCompanyClientOptions.!tooltip.client_group_id'] = 'The custom field will only apply to member\'s of the selected client group.';
$lang['AdminCompanyClientOptions.!tooltip.name'] = 'This is the display name for this field. It may be a language definition.';
$lang['AdminCompanyClientOptions.!tooltip.is_lang'] = 'Only check this box if you have added a language definition for this custom field in the custom language file.';
$lang['AdminCompanyClientOptions.!tooltip.type'] = 'The custom field will appear as the selected form type.';
$lang['AdminCompanyClientOptions.!tooltip.show_client'] = 'Check to allow clients to see and update this field.';
$lang['AdminCompanyClientOptions.!tooltip.read_only'] = 'Checking this box will make this custom field unchangeable by the client.';
$lang['AdminCompanyClientOptions.!tooltip.required'] = 'Select "Yes" to ensure that a value is given for this field, for Drop Down types the option must appear in the list of options. Select "No" to accept any value for this field. Select "Custom Regex" to use a custom regular expression to valid this field.';
$lang['AdminCompanyClientOptions.!tooltip.regex'] = 'This option will appear if "Required" is set to "Custom Regex". Enter the custom regular expression to validate for this field here.';
$lang['AdminCompanyClientOptions.!tooltip.encrypted'] = 'Check this box to store the value encrypted. This is highly recommended if storing any sensitive or personally identifying information.';

$lang['AdminCompanyClientOptions.!tooltip.checkbox_value'] = 'The value submitted when the checkbox is checked.';
$lang['AdminCompanyClientOptions.!tooltip.default_text'] = 'The text entered here will be the default value set for this option when this custom field is added for a client.';
$lang['AdminCompanyClientOptions.!tooltip.default_checkbox'] = 'If checked, this checkbox will be checked by default when this custom field is added for a client.';

$lang['AdminCompanyClientOptions.!tooltip.select_default'] = 'The checked option value will be the default value selected when this option is added for a client.';


// General settings
$lang['AdminCompanyClientoptions.general.page_title'] = 'Settings > Company > Client Options > General';
$lang['AdminCompanyClientOptions.general.boxtitle'] = 'General Client Settings';

$lang['AdminCompanyClientOptions.general.field_unique_contact_emails'] = 'Enforce Unique Contact Email Addresses';
$lang['AdminCompanyClientOptions.general.field_unique_contact_emails_none'] = '-- None --';
$lang['AdminCompanyClientOptions.general.field_unique_contact_emails_primary'] = 'Primary Contacts';
$lang['AdminCompanyClientOptions.general.field_unique_contact_emails_all'] = 'All Contacts';
$lang['AdminCompanyClientOptions.general.text_submit'] = 'Submit';

// Custom Client Fields
$lang['AdminCompanyClientoptions.customfields.page_title'] = 'Settings > Company > Client Options > Client Custom Fields > Browse';
$lang['AdminCompanyClientOptions.customfields.boxtitle_browse'] = 'Browse Client Custom Fields';
$lang['AdminCompanyClientOptions.customfields.categorylink_addfield'] = 'Create Field';

$lang['AdminCompanyClientOptions.customfields.text_name'] = 'Name';
$lang['AdminCompanyClientOptions.customfields.text_type'] = 'Type';
$lang['AdminCompanyClientOptions.customfields.text_required'] = 'Required';
$lang['AdminCompanyClientOptions.customfields.text_visible'] = 'Visible to Clients';
$lang['AdminCompanyClientOptions.customfields.text_read_only'] = 'Read Only for Clients';
$lang['AdminCompanyClientOptions.customfields.text_options'] = 'Options';

$lang['AdminCompanyClientOptions.customfields.option_edit'] = 'Edit';
$lang['AdminCompanyClientOptions.customfields.option_delete'] = 'Delete';
$lang['AdminCompanyClientOptions.customfields.confirm_delete'] = 'Deleting this custom field will delete any and all data stored for it for each client within this group. Are you sure you want to delete this custom field?';

$lang['AdminCompanyClientOptions.customfields.no_results'] = 'There are no custom fields.';


// Add Custom Field
$lang['AdminCompanyClientoptions.addcustomfield.page_title'] = 'Settings > Company > Client Options > Client Custom Fields > Add Custom Field';
$lang['AdminCompanyClientOptions.addcustomfield.boxtitle_add'] = 'Add Custom Field';

$lang['AdminCompanyClientOptions.addcustomfield.field.client_group_id'] = 'Client Group';
$lang['AdminCompanyClientOptions.addcustomfield.field.name'] = 'Name';
$lang['AdminCompanyClientOptions.addcustomfield.field.is_lang'] = 'Name is a language definition';
$lang['AdminCompanyClientOptions.addcustomfield.field.type'] = 'Type';
$lang['AdminCompanyClientOptions.addcustomfield.field.show_client'] = 'Visible to Clients';
$lang['AdminCompanyClientOptions.addcustomfield.field.read_only'] = 'Read Only for Clients';
$lang['AdminCompanyClientOptions.addcustomfield.field.required'] = 'Required';
$lang['AdminCompanyClientOptions.addcustomfield.field.regex'] = 'Custom Regex';
$lang['AdminCompanyClientOptions.addcustomfield.field.encrypted'] = 'Encrypt Values';
$lang['AdminCompanyClientOptions.addcustomfield.field.addsubmit'] = 'Add Custom Field';

$lang['AdminCompanyClientOptions.addcustomfield.field.checkbox_value'] = 'Value';
$lang['AdminCompanyClientOptions.addcustomfield.field.default_checkbox'] = 'Default Value Checked';
$lang['AdminCompanyClientOptions.addcustomfield.field.default_text'] = 'Default Text Value';

$lang['AdminCompanyClientOptions.addcustomfield.configuration_warning'] = 'Requiring this field while not making it visible to clients will result in clients being unable to register or update their account information.';
$lang['AdminCompanyClientOptions.addcustomfield.categorylink_select'] = 'Add Additional Option';
$lang['AdminCompanyClientOptions.addcustomfield.heading_select_value'] = 'Value';
$lang['AdminCompanyClientOptions.addcustomfield.heading_select_option'] = 'Option Name';
$lang['AdminCompanyClientOptions.addcustomfield.heading_select_default'] = 'Default';
$lang['AdminCompanyClientOptions.addcustomfield.text_remove'] = 'Remove';


// Edit Custom Field
$lang['AdminCompanyClientoptions.editcustomfield.page_title'] = 'Settings > Company > Client Options > Client Custom Fields > Edit Custom Field';
$lang['AdminCompanyClientOptions.editcustomfield.boxtitle_edit'] = 'Edit Custom Field';

$lang['AdminCompanyClientOptions.editcustomfield.field.name'] = 'Name';
$lang['AdminCompanyClientOptions.editcustomfield.field.is_lang'] = 'Name is a language definition';
$lang['AdminCompanyClientOptions.editcustomfield.field.type'] = 'Type';
$lang['AdminCompanyClientOptions.editcustomfield.field.show_client'] = 'Visible to Clients';
$lang['AdminCompanyClientOptions.editcustomfield.field.read_only'] = 'Read Only for Clients';
$lang['AdminCompanyClientOptions.editcustomfield.field.required'] = 'Required';
$lang['AdminCompanyClientOptions.editcustomfield.field.regex'] = 'Custom Regex';
$lang['AdminCompanyClientOptions.editcustomfield.field.encrypted'] = 'Encrypt Values';
$lang['AdminCompanyClientOptions.editcustomfield.field.editsubmit'] = 'Edit Custom Field';

$lang['AdminCompanyClientOptions.editcustomfield.field.checkbox_value'] = 'Value';
$lang['AdminCompanyClientOptions.editcustomfield.field.default_checkbox'] = 'Default Value Checked';
$lang['AdminCompanyClientOptions.editcustomfield.field.default_text'] = 'Default Text Value';

$lang['AdminCompanyClientOptions.editcustomfield.categorylink_select'] = 'Add Additional Option';
$lang['AdminCompanyClientOptions.editcustomfield.heading_select_value'] = 'Value';
$lang['AdminCompanyClientOptions.editcustomfield.heading_select_option'] = 'Option Name';
$lang['AdminCompanyClientOptions.editcustomfield.heading_select_default'] = 'Default';
$lang['AdminCompanyClientOptions.editcustomfield.text_remove'] = 'Remove';


// Text
$lang['AdminCompanyClientOptions.getRequired.no'] = 'No';
$lang['AdminCompanyClientOptions.getRequired.yes'] = 'Yes';
$lang['AdminCompanyClientOptions.getRequired.regex'] = 'Custom Regex';


// Require Fields
$lang['AdminCompanyClientoptions.requiredfields.page_title'] = 'Settings > Company > Client Options > Client Custom Fields > Required Client Fields';
$lang['AdminCompanyClientOptions.requiredfields.boxtitle'] = 'Required Client Fields';
$lang['AdminCompanyClientOptions.requiredfields.description'] = 'Check the fields that should be required when creating or updating a client or contact.';

$lang['AdminCompanyClientOptions.requiredfields.field_first_name'] = 'First Name';
$lang['AdminCompanyClientOptions.requiredfields.field_last_name'] = 'Last Name';
$lang['AdminCompanyClientOptions.requiredfields.field_company'] = 'Company/Org.';
$lang['AdminCompanyClientOptions.requiredfields.field_title'] = 'Title';
$lang['AdminCompanyClientOptions.requiredfields.field_address1'] = 'Address 1';
$lang['AdminCompanyClientOptions.requiredfields.field_address2'] = 'Address 2';
$lang['AdminCompanyClientOptions.requiredfields.field_city'] = 'City';
$lang['AdminCompanyClientOptions.requiredfields.field_country'] = 'Country';
$lang['AdminCompanyClientOptions.requiredfields.field_state'] = 'State/Province';
$lang['AdminCompanyClientOptions.requiredfields.field_zip'] = 'Zip/Postal Code';
$lang['AdminCompanyClientOptions.requiredfields.field_email'] = 'Email';

$lang['AdminCompanyClientOptions.requiredfields.text_submit'] = 'Update Settings';
