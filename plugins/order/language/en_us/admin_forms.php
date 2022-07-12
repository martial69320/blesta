<?php
// Success
$lang['AdminForms.!success.settings_saved'] = "The settings were successfully saved";
$lang['AdminForms.!success.form_added'] = "The form was successfully added.";
$lang['AdminForms.!success.form_delete'] = "The form was successfully deleted.";
$lang['AdminForms.!success.form_edited'] = "The form was successfully updated.";

// Page titles
$lang['AdminForms.index.page_title'] = "Order System";
$lang['AdminForms.settings.page_title'] = "Order System";
$lang['AdminForms.add.page_title'] = "Order System";
$lang['AdminForms.edit.page_title'] = "Order System";

// Index (order forms)
$lang['AdminForms.index.boxtitle_order'] = "Order System";
$lang['AdminForms.index.tab_forms'] = "Order Forms";
$lang['AdminForms.index.tab_settings'] = "Settings";
$lang['AdminForms.index.order_form_add'] = "Add Order Form";
$lang['AdminForms.index.order_forms_no_results'] = "There are no order forms at this time.";
$lang['AdminForms.index.heading_name'] = "Name";
$lang['AdminForms.index.heading_label'] = "Label";
$lang['AdminForms.index.heading_template'] = "Template";
$lang['AdminForms.index.heading_type'] = "Type";
$lang['AdminForms.index.heading_date_added'] = "Date Added";
$lang['AdminForms.index.heading_status'] = "Status";
$lang['AdminForms.index.heading_options'] = "Options";
$lang['AdminForms.index.option_edit'] = "Edit";
$lang['AdminForms.index.option_delete'] = "Delete";
$lang['AdminForms.index.text_confirm_delete'] = "Really delete this order form?";
$lang['AdminForms.index.field_status_active'] = "Active";
$lang['AdminForms.index.field_status_inactive'] = "Inactive";

// Add
$lang['AdminForms.add.boxtitle_order'] = "Order System - Add Form";
$lang['AdminForms.add.field_name'] = "Name";
$lang['AdminForms.add.field_label'] = "Label";
$lang['AdminForms.add.field_description'] = "Description";
$lang['AdminForms.add.field_visibility'] = "Visibility";
$lang['AdminForms.add.field_type'] = "Type";
$lang['AdminForms.add.field_allow_coupons'] = "Allow Coupons";
$lang['AdminForms.add.field_manual_review'] = "Require Manual Review and Approval of All Orders";
$lang['AdminForms.add.field_require_ssl'] = "Force Secure Connection (HTTPS)";
$lang['AdminForms.add.field_require_captcha'] = "Require Human Verification Challenge for All Signups";
$lang['AdminForms.add.field_require_tos'] = "Require Agreement to Terms of Service";
$lang['AdminForms.add.field_tos_url'] = "Terms of Service URL";
$lang['AdminForms.add.field_status'] = "Status";
$lang['AdminForms.add.field_status_active'] = "Active";
$lang['AdminForms.add.field_status_inactive'] = "Inactive";
$lang['AdminForms.add.field_template'] = "Template";
$lang['AdminForms.add.template_preview'] = "Preview";
$lang['AdminForms.add.field_client_group_id'] = "Default Client Group";
$lang['AdminForms.add.tooltip_client_group'] = "The default client group new signups from this form will be assigned to.";
$lang['AdminForms.add.field_addsubmit'] = "Add Form";
$lang['AdminForms.add.heading_basic'] = "Basic";

// Edit
$lang['AdminForms.edit.boxtitle_order'] = "Order System - Edit Form";
$lang['AdminForms.edit.field_name'] = "Name";
$lang['AdminForms.edit.field_label'] = "Label";
$lang['AdminForms.edit.field_description'] = "Description";
$lang['AdminForms.edit.field_visibility'] = "Visibility";
$lang['AdminForms.edit.field_type'] = "Type";
$lang['AdminForms.edit.field_allow_coupons'] = "Allow Coupons";
$lang['AdminForms.edit.field_manual_review'] = "Require Manual Review and Approval of All Orders";
$lang['AdminForms.edit.field_require_ssl'] = "Force Secure Connection (HTTPS)";
$lang['AdminForms.edit.field_require_tos'] = "Require Agreement to Terms of Service";
$lang['AdminForms.edit.field_require_captcha'] = "Require Human Verification Challenge for All Signups";
$lang['AdminForms.edit.field_tos_url'] = "Terms of Service URL";
$lang['AdminForms.edit.field_status'] = "Status";
$lang['AdminForms.edit.field_status_active'] = "Active";
$lang['AdminForms.edit.field_status_inactive'] = "Inactive";
$lang['AdminForms.edit.field_template'] = "Template";
$lang['AdminForms.edit.template_preview'] = "Preview";
$lang['AdminForms.edit.field_client_group_id'] = "Default Client Group";
$lang['AdminForms.edit.tooltip_client_group'] = "The default client group new signups from this form will be assigned to.";
$lang['AdminForms.edit.field_addsubmit'] = "Edit Form";
$lang['AdminForms.edit.heading_basic'] = "Basic";

// Meta
$lang['AdminForms.meta.text_membergroups'] = "Assigned Groups";
$lang['AdminForms.meta.text_availablegroups'] = "Available Groups";
$lang['AdminForms.meta.text_drag_and_drop'] = "Drag & Drop Groups Here";
$lang['AdminForms.meta.heading_package_groups'] = "Package Groups";
$lang['AdminForms.meta.heading_package_group'] = "Package Group";
$lang['AdminForms.meta.heading_currencies'] = "Currencies";
$lang['AdminForms.meta.heading_gateways'] = "Gateways";

$lang['AdminForms.meta.tooltip_text_membergroups'] = "Package groups will appear on the order form in the order seen here.";

// Settings
$lang['AdminForms.settings.boxtitle_order'] = "Order System";
$lang['AdminForms.settings.basic_heading'] = "Basic Options";
$lang['AdminForms.settings.field_default_form'] = "Default Order Form";
$lang['AdminForms.settings.default_form.none'] = "None - Show Listing";
$lang['AdminForms.settings.field_captcha'] = "Human Verification";
$lang['AdminForms.settings.field_captcha_none'] = "None";
$lang['AdminForms.settings.field_captcha_recaptcha'] = "reCaptcha";
$lang['AdminForms.settings.field_recaptcha_pub_key'] = "reCaptcha Site Key";
$lang['AdminForms.settings.field_recaptcha_shared_key'] = "reCaptcha Shared Key";
$lang['AdminForms.settings.antifraud_heading'] = "Anti-Fraud";
$lang['AdminForms.settings.field_antifraud'] = "Type";
$lang['AdminForms.settings.field_antifraud.none'] = "None";
$lang['AdminForms.settings.field_antifraud_frequency'] = 'Anti-Fraud Frequency';
$lang['AdminForms.settings.field_antifraud_frequency.always'] = 'Run fraud checks for all orders and customer signups';
$lang['AdminForms.settings.field_antifraud_frequency.new'] = 'Run fraud checks for customers signups only';
$lang['AdminForms.settings.embed_code_heading'] = "Embed Code";
$lang['AdminForms.settings.field_tags'] = "Tags";
$lang['AdminForms.settings.field_embed_code'] = "Order Form Embed Code";
$lang['AdminForms.settings.marketing_heading'] = "Marketing";
$lang['AdminForms.settings.field_marketing_default'] = "Client Email Marketing";
$lang['AdminForms.settings.field_marketing_default.true'] = "The option to receive email marketing will be selected by default";
$lang['AdminForms.settings.field_marketing_default.false'] = "The option to receive email marketing must be selected by the client";
$lang['AdminForms.settings.tooltip_marketing_default_true'] = "This option will be shown on the client registration page, selected to opt them in to email marketing.";
$lang['AdminForms.settings.tooltip_marketing_default_false'] = "This option will be shown on the client registration page, deselected to opt them out of email marketing.";

$lang['AdminForms.settings.field_save'] = "Save Settings";
