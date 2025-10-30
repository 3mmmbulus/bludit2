<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/cache-list');

// Handle clear cache
if (isset($_POST['clear'])) {
    SystemIntegrity::isAuthorized();
    
    // Process cache clear
    // Implementation here
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'cache-list';
