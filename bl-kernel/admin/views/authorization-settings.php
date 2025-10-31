<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>authorization-settings.css">

<div class="authorization-settings">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType ?>">
        <?php echo $message ?>
    </div>
    <?php endif; ?>
    
    <div class="upload-box">
        <h3><?php echo $pageL->get('label_upload_license') ?></h3>
        
        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="file" name="license" accept=".json" required>
            </div>
            <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_upload') ?></button>
        </form>
    </div>
    
    <?php if (is_readable(PATH_AUTHZ . 'license.json')): ?>
    <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 4px;">
        <strong>✓ License file is installed</strong>
    </div>
    <?php endif; ?>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>authorization-settings.js"></script>
