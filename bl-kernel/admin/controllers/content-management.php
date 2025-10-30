<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/content-management');

// Handle delete action
if (isset($_POST['delete'])) {
    SystemIntegrity::isAuthorized();
    
    // Process delete
    // Implementation here
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'content-management';
