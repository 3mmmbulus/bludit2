<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>about-maigewan.css">

<div class="about-maigewan">
    <div class="logo-section">
        <h1>MAIGEWAN</h1>
        <p><?php echo $pageL->get('description') ?></p>
    </div>
    
    <div class="info-card">
        <h3><?php echo $pageL->get('version') ?></h3>
        <p><?php echo BLUDIT_VERSION ?> (<?php echo BLUDIT_CODENAME ?>)</p>
        <p>Build: <?php echo BLUDIT_BUILD ?></p>
        <p>Release Date: <?php echo BLUDIT_RELEASE_DATE ?></p>
    </div>
    
    <div class="info-card">
        <h3><?php echo $pageL->get('copyright') ?></h3>
        <p>&copy; <?php echo date('Y') ?> MAIGEWAN. All rights reserved.</p>
    </div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>about-maigewan.js"></script>
