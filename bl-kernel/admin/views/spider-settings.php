<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>spider-settings.css">

<div class="spider-settings">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="checkbox-group">
            <input type="checkbox" name="enable_logging" id="enable_logging">
            <label for="enable_logging"><?php echo $pageL->get('label_enable_logging') ?></label>
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_whitelist') ?></label>
            <textarea name="whitelist" placeholder="Googlebot&#10;Bingbot&#10;..."></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>spider-settings.js"></script>
