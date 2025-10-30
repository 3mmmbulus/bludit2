<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>brand-logo.css">

<div class="brand-logo">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <div class="logo-preview">
        <!-- Logo preview will be displayed here -->
    </div>
    
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="logo" accept="image/*">
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_upload') ?></button>
    </form>
    
    <form method="post" style="margin-top: 20px;">
        <button type="submit" name="remove" class="btn btn-danger"><?php echo $pageL->get('btn_remove') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>brand-logo.js"></script>
