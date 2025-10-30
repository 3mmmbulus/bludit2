<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/about-maigewan');

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'about-maigewan';
