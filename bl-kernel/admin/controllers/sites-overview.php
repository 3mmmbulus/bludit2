<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Permission check
checkRole(['admin', 'editor'], false);

// Load page i18n
$pageL = new Language('pages/sites-overview');

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'sites-overview';
