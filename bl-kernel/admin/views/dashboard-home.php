<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>dashboard-home.css">

<div class="dashboard-home">
    <h2><?php echo $pageL->get('welcome') ?></h2>
    
    <div class="row">
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo $pageL->get('total_pages') ?></h3>
                <div class="number"><?php echo $pages->count() ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <h3><?php echo $pageL->get('total_users') ?></h3>
                <div class="number"><?php echo $users->count() ?></div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>dashboard-home.js"></script>
