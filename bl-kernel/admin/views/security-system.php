<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>security-system.css">

<div class="security-system">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="mb-3">
            <label><?php echo $pageL->get('label_ip_whitelist') ?></label>
            <textarea name="ip_whitelist" placeholder="192.168.1.1&#10;10.0.0.0/24"></textarea>
        </div>
        
        <div class="checkbox-group">
            <input type="checkbox" name="brute_force_protection" id="brute_force_protection">
            <label for="brute_force_protection"><?php echo $pageL->get('label_brute_force_protection') ?></label>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>security-system.js"></script>
