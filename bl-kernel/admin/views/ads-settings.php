<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>ads-settings.css">

<div class="ads-settings">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="form-group">
            <label><?php echo $pageL->get('label_ad_code') ?></label>
            <textarea name="ad_code" placeholder="<script>...</script>"></textarea>
        </div>
        
        <div class="form-group">
            <label><?php echo $pageL->get('label_position') ?></label>
            <select name="position" class="form-control">
                <option value="header">Header</option>
                <option value="sidebar">Sidebar</option>
                <option value="footer">Footer</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>ads-settings.js"></script>
