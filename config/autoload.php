<?php

ClassLoader::addNamespace( 'CatalogManager' );

ClassLoader::addClasses([

    'CatalogManager\tl_page' => 'system/modules/catalog-manager/classes/tl_page.php',
    'CatalogManager\Map' => 'system/modules/catalog-manager/library/alnv/Fields/Map.php',
    'CatalogManager\tl_module' => 'system/modules/catalog-manager/classes/tl_module.php',
    'CatalogManager\Toolkit' => 'system/modules/catalog-manager/library/alnv/Toolkit.php',
    'CatalogManager\tl_catalog' => 'system/modules/catalog-manager/classes/tl_catalog.php',
    'CatalogManager\tl_content' => 'system/modules/catalog-manager/classes/tl_content.php',
    'CatalogManager\Text' => 'system/modules/catalog-manager/library/alnv/Fields/Text.php',
    'CatalogManager\tl_settings' => 'system/modules/catalog-manager/classes/tl_settings.php',
    'CatalogManager\Radio' => 'system/modules/catalog-manager/library/alnv/Fields/Radio.php',
    'CatalogManager\DcBuilder' => 'system/modules/catalog-manager/library/alnv/DcBuilder.php',
    'CatalogManager\Select' => 'system/modules/catalog-manager/library/alnv/Fields/Select.php',
    'CatalogManager\Hidden' => 'system/modules/catalog-manager/library/alnv/Fields/Hidden.php',
    'CatalogManager\Upload' => 'system/modules/catalog-manager/library/alnv/Fields/Upload.php',
    'CatalogManager\Number' => 'system/modules/catalog-manager/library/alnv/Fields/Number.php',
    'CatalogManager\IconGetter' => 'system/modules/catalog-manager/library/alnv/IconGetter.php',
    'CatalogManager\CSVBuilder' => 'system/modules/catalog-manager/library/alnv/CSVBuilder.php',
    'CatalogManager\SQLBuilder' => 'system/modules/catalog-manager/library/alnv/SQLBuilder.php',
    'CatalogManager\DcModifier' => 'system/modules/catalog-manager/library/alnv/DcModifier.php',
    'CatalogManager\DcCallbacks' => 'system/modules/catalog-manager/library/alnv/DcCallbacks.php',
    'CatalogManager\CatalogView' => 'system/modules/catalog-manager/library/alnv/CatalogView.php',
    'CatalogManager\Checkbox' => 'system/modules/catalog-manager/library/alnv/Fields/Checkbox.php',
    'CatalogManager\Textarea' => 'system/modules/catalog-manager/library/alnv/Fields/Textarea.php',
    'CatalogManager\GeoCoding' => 'system/modules/catalog-manager/library/alnv/Maps/GeoCoding.php',
    'CatalogManager\DbColumn' => 'system/modules/catalog-manager/library/alnv/Fields/DbColumn.php',
    'CatalogManager\DcPermission' => 'system/modules/catalog-manager/library/alnv/DcPermission.php',
    'CatalogManager\CatalogInput' => 'system/modules/catalog-manager/library/alnv/CatalogInput.php',
    'CatalogManager\tl_catalog_form' => 'system/modules/catalog-manager/classes/tl_catalog_form.php',
    'CatalogManager\DateInput' => 'system/modules/catalog-manager/library/alnv/Fields/DateInput.php',
    'CatalogManager\OptionsGetter' => 'system/modules/catalog-manager/library/alnv/OptionsGetter.php',
    'CatalogManager\CatalogFilter' => 'system/modules/catalog-manager/library/alnv/CatalogFilter.php',
    'CatalogManager\CatalogEvents' => 'system/modules/catalog-manager/library/alnv/CatalogEvents.php',
    'CatalogManager\OrderByHelper' => 'system/modules/catalog-manager/library/alnv/OrderByHelper.php',
    'CatalogManager\CatalogMessage' => 'system/modules/catalog-manager/library/alnv/CatalogMessage.php',
    'CatalogManager\GalleryCreator' => 'system/modules/catalog-manager/library/alnv/GalleryCreator.php',
    'CatalogManager\SQLQueryHelper' => 'system/modules/catalog-manager/library/alnv/SQLQueryHelper.php',
    'CatalogManager\TemplateHelper' => 'system/modules/catalog-manager/library/alnv/TemplateHelper.php',
    'CatalogManager\RoutingBuilder' => 'system/modules/catalog-manager/library/alnv/RoutingBuilder.php',
    'CatalogManager\tl_catalog_fields' => 'system/modules/catalog-manager/classes/tl_catalog_fields.php',
    'CatalogManager\CatalogTaxonomy' => 'system/modules/catalog-manager/library/alnv/CatalogTaxonomy.php',
    'CatalogManager\SQLQueryBuilder' => 'system/modules/catalog-manager/library/alnv/SQLQueryBuilder.php',
    'CatalogManager\FrontendEditing' => 'system/modules/catalog-manager/library/alnv/FrontendEditing.php',
    'CatalogManager\MessageInput' => 'system/modules/catalog-manager/library/alnv/Fields/MessageInput.php',
    'CatalogManager\CatalogDcAdapter' => 'system/modules/catalog-manager/library/alnv/CatalogDcAdapter.php',
    'CatalogManager\DownloadsCreator' => 'system/modules/catalog-manager/library/alnv/DownloadsCreator.php',
    'CatalogManager\CatalogController' => 'system/modules/catalog-manager/library/alnv/CatalogController.php',
    'CatalogManager\CatalogBreadcrumb' => 'system/modules/catalog-manager/library/alnv/CatalogBreadcrumb.php',
    'CatalogManager\SearchIndexBuilder' => 'system/modules/catalog-manager/library/alnv/SearchIndexBuilder.php',
    'CatalogManager\CatalogSQLCompiler' => 'system/modules/catalog-manager/library/alnv/CatalogSQLCompiler.php',
    'CatalogManager\CatalogDcExtractor' => 'system/modules/catalog-manager/library/alnv/CatalogDcExtractor.php',
    'CatalogManager\CatalogFieldBuilder' => 'system/modules/catalog-manager/library/alnv/CatalogFieldBuilder.php',
    'CatalogManager\CatalogFineUploader' => 'system/modules/catalog-manager/library/alnv/CatalogFineUploader.php',
    'CatalogManager\CatalogNotification' => 'system/modules/catalog-manager/library/alnv/CatalogNotification.php',
    'CatalogManager\ReviseRelatedTables' => 'system/modules/catalog-manager/library/alnv/ReviseRelatedTables.php',
    'CatalogManager\tl_catalog_form_fields' => 'system/modules/catalog-manager/classes/tl_catalog_form_fields.php',
    'CatalogManager\ModuleMasterView' => 'system/modules/catalog-manager/library/alnv/Modules/ModuleMasterView.php',
    'CatalogManager\ActiveInsertTag' => 'system/modules/catalog-manager/library/alnv/Inserttags/ActiveInsertTag.php',
    'CatalogManager\MasterInsertTag' => 'system/modules/catalog-manager/library/alnv/Inserttags/MasterInsertTag.php',
    'CatalogManager\CatalogMessageForm' => 'system/modules/catalog-manager/library/alnv/Forms/CatalogMessageForm.php',
    'CatalogManager\I18nCatalogTranslator' => 'system/modules/catalog-manager/library/alnv/I18nCatalogTranslator.php',
    'CatalogManager\CatalogAjaxController' => 'system/modules/catalog-manager/library/alnv/CatalogAjaxController.php',
    'CatalogManager\CatalogAutoCompletion' => 'system/modules/catalog-manager/library/alnv/CatalogAutoCompletion.php',
    'CatalogManager\CatalogDatabaseBuilder' => 'system/modules/catalog-manager/library/alnv/CatalogDatabaseBuilder.php',
    'CatalogManager\ChangeLanguageExtension' => 'system/modules/catalog-manager/library/alnv/ChangeLanguageExtension.php',
    'CatalogManager\ModuleUniversalView' => 'system/modules/catalog-manager/library/alnv/Modules/ModuleUniversalView.php',
    'CatalogManager\UserPermissionExtension' => 'system/modules/catalog-manager/library/alnv/UserPermissionExtension.php',
    'CatalogManager\ModuleCatalogFilter' => 'system/modules/catalog-manager/library/alnv/Modules/ModuleCatalogFilter.php',
    'CatalogManager\CatalogMessageWidget' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogMessageWidget.php',
    'CatalogManager\CatalogTaxonomyWizard' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogTaxonomyWizard.php',
    'CatalogManager\MemberPermissionExtension' => 'system/modules/catalog-manager/library/alnv/MemberPermissionExtension.php',
    'CatalogManager\FrontendEditingPermission' => 'system/modules/catalog-manager/library/alnv/FrontendEditingPermission.php',
    'CatalogManager\CatalogManagerInitializer' => 'system/modules/catalog-manager/library/alnv/CatalogManagerInitializer.php',
    'CatalogManager\CatalogTextFieldWidget' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogTextFieldWidget.php',
    'CatalogManager\CatalogFineUploaderForm' => 'system/modules/catalog-manager/library/alnv/Forms/CatalogFineUploaderForm.php',
    'CatalogManager\CatalogManagerVerification' => 'system/modules/catalog-manager/library/alnv/CatalogManagerVerification.php',
    'CatalogManager\FilterValuesInsertTag' => 'system/modules/catalog-manager/library/alnv/Inserttags/FilterValuesInsertTag.php',
    'CatalogManager\CatalogValueSetterWizard' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogValueSetterWizard.php',
    'CatalogManager\CatalogWidgetAttributeParser' => 'system/modules/catalog-manager/library/alnv/CatalogWidgetAttributeParser.php',
    'CatalogManager\ContentCatalogFilterForm' => 'system/modules/catalog-manager/library/alnv/Elements/ContentCatalogFilterForm.php',
    'CatalogManager\ModuleCatalogTaxonomyTree' => 'system/modules/catalog-manager/library/alnv/Modules/ModuleCatalogTaxonomyTree.php',
    'CatalogManager\CatalogDuplexSelectWizard' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogDuplexSelectWizard.php',
    'CatalogManager\CatalogRelationRedirectWizard' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogRelationRedirectWizard.php',
    'CatalogManager\CatalogFilterFieldSelectWizard' => 'system/modules/catalog-manager/library/alnv/Widgets/CatalogFilterFieldSelectWizard.php'
]);

TemplateLoader::addFiles([

    'ctlg_field_map' => 'system/modules/catalog-manager/templates',
    'ctlg_view_table' => 'system/modules/catalog-manager/templates',
    'ctlg_view_teaser' => 'system/modules/catalog-manager/templates',
    'ctlg_view_master' => 'system/modules/catalog-manager/templates',
    'ctlg_map_default' => 'system/modules/catalog-manager/templates',
    'ctlg_taxonomy_nav' => 'system/modules/catalog-manager/templates',
    'mod_catalog_table' => 'system/modules/catalog-manager/templates',
    'ctlg_form_default' => 'system/modules/catalog-manager/templates',
    'ctlg_form_grouped' => 'system/modules/catalog-manager/templates',
    'ctlg_form_message' => 'system/modules/catalog-manager/templates',
    'mod_catalog_filter' => 'system/modules/catalog-manager/templates',
    'mod_catalog_master' => 'system/modules/catalog-manager/templates',
    'ctlg_debug_default' => 'system/modules/catalog-manager/templates',
    'ctlg_message_default' => 'system/modules/catalog-manager/templates',
    'ctlg_catalog_tinyMCE' => 'system/modules/catalog-manager/templates',
    'mod_catalog_taxonomy' => 'system/modules/catalog-manager/templates',
    'ctlg_form_field_text' => 'system/modules/catalog-manager/templates',
    'ctlg_form_field_radio' => 'system/modules/catalog-manager/templates',
    'ctlg_form_field_range' => 'system/modules/catalog-manager/templates',
    'mod_catalog_universal' => 'system/modules/catalog-manager/templates',
    'ce_catalog_filterform' => 'system/modules/catalog-manager/templates',
    'ctlg_form_field_select' => 'system/modules/catalog-manager/templates',
    'ctlg_form_fine_uploader' => 'system/modules/catalog-manager/templates',
    'mod_catalog_map_default' => 'system/modules/catalog-manager/templates',
    'ctlg_form_field_checkbox' => 'system/modules/catalog-manager/templates',
]);