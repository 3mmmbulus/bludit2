<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>profile.css">

<div class="profile">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <form method="post">
        <div class="mb-3">
            <label><?php echo $pageL->get('label_username') ?></label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($currentUser->username()) ?>" readonly>
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_email') ?></label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser->email()) ?>">
        </div>
        
        <div class="mb-3">
            <label><?php echo $pageL->get('label_password') ?></label>
            <input type="password" name="password" placeholder="Leave blank to keep current password">
        </div>
        
        <button type="submit" class="btn btn-primary"><?php echo $pageL->get('btn_save') ?></button>
    </form>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>profile.js"></script>
