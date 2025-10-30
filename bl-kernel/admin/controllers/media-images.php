<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/media-images');

// Handle upload
if (isset($_FILES['image'])) {
    SystemIntegrity::isAuthorized();
    
    // Process upload
    // Implementation here
}

// Handle delete
if (isset($_POST['delete'])) {
    SystemIntegrity::isAuthorized();
    
    // Process delete
    // Implementation here
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'media-images';
