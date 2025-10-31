<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>seo-settings.css">

<div class="seo-settings">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="mb-3">
            <label><?php echo $pageL->get('label_meta_description') ?></label>
            <textarea name="meta_description"></textarea>
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_meta_keywords') ?></label>
            <textarea name="meta_keywords"></textarea>
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_robots_txt') ?></label>
            <textarea name="robots_txt"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>seo-settings.js"></script>
