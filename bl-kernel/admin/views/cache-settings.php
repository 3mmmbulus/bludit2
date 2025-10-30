<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>cache-settings.css">

<div class="cache-settings">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="form-group checkbox-group">
            <input type="checkbox" name="enable_cache" id="enable_cache">
            <label for="enable_cache"><?php echo $pageL->get('label_enable_cache') ?></label>
        </div>
        
        <div class="form-group">
            <label><?php echo $pageL->get('label_ttl') ?></label>
            <input type="number" name="cache_ttl" value="3600" min="0">
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>cache-settings.js"></script>
