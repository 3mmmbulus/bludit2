<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>security-general.css">

<div class="security-general">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="checkbox-group">
            <input type="checkbox" name="https_only" id="https_only">
            <label for="https_only"><?php echo $pageL->get('label_https_only') ?></label>
        </div>
        
        <div class="checkbox-group">
            <input type="checkbox" name="csrf_protection" id="csrf_protection" checked>
            <label for="csrf_protection"><?php echo $pageL->get('label_csrf_protection') ?></label>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>security-general.js"></script>
