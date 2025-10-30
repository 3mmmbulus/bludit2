<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Permission check
checkRole(['admin'], false);

// Load page i18n
$pageL = new Language('pages/site-new');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    SystemIntegrity::isAuthorized();
    
    // Process site creation
    // Implementation here
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'site-new';
