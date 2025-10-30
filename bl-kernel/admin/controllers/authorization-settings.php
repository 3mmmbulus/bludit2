<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/authorization-settings');

$message = '';
$messageType = '';

// Check if reason parameter exists
if (isset($_GET['reason']) && $_GET['reason'] === 'missing') {
    $message = $pageL->get('msg_missing');
    $messageType = 'warning';
}

// Handle license upload
if (isset($_FILES['license'])) {
    // Note: Authorization check is special here - we're uploading the license itself
    // So we don't call isAuthorized() to avoid circular dependency
    
    $licenseFile = PATH_AUTHZ . 'license.json';
    
    if ($_FILES['license']['error'] === UPLOAD_ERR_OK) {
        $tmpFile = $_FILES['license']['tmp_name'];
        
        // Validate JSON
        $content = file_get_contents($tmpFile);
        $data = json_decode($content, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            // Move file
            if (move_uploaded_file($tmpFile, $licenseFile)) {
                $message = $pageL->get('msg_uploaded');
                $messageType = 'success';
            }
        } else {
            $message = $pageL->get('msg_invalid');
            $messageType = 'warning';
        }
    }
}

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'authorization-settings';
