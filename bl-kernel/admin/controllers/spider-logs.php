<?php defined('BLUDIT') or die('Bludit CMS.');

// SystemIntegrity check
SystemIntegrity::quickCheck();

// Load page i18n
$pageL = new Language('pages/spider-logs');

// Build page
$layout['title'] = $pageL->get('title');
$layout['view'] = 'spider-logs';
