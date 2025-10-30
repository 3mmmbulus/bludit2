<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>media-images.css">

<div class="media-images">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <div class="toolbar">
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="image" accept="image/*">
            <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_upload') ?></button>
        </form>
    </div>
    
    <div class="image-grid">
        <!-- Images will be displayed here -->
    </div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>media-images.js"></script>
