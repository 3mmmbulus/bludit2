<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>site-new.css">

<div class="site-new">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="mb-3">
            <label><?php echo $pageL->get('label_domain') ?></label>
            <input type="text" name="domain" required>
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_site_name') ?></label>
            <input type="text" name="site_name" required>
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_create') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>site-new.js"></script>
