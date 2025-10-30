<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>system-repair-upgrade.css">

<div class="system-repair-upgrade">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <div class="action-card">
        <h3>System Verification</h3>
        <p>Check system integrity and file consistency.</p>
        <form method="post">
            <div class="btn-group">
                <button type="submit" name="verify" class="btn btn-primary"><?php echo $pageL->get('btn_verify') ?></button>
            </div>
        </form>
        <?php if ($verifyResult): ?>
        <div class="result-box"><?php echo htmlspecialchars($verifyResult) ?></div>
        <?php endif; ?>
    </div>
    
    <div class="action-card">
        <h3>System Repair</h3>
        <p>Repair missing or corrupted files.</p>
        <form method="post">
            <div class="btn-group">
                <button type="submit" name="repair" class="btn btn-warning"><?php echo $pageL->get('btn_repair') ?></button>
            </div>
        </form>
    </div>
    
    <div class="action-card">
        <h3>System Upgrade</h3>
        <p>Upgrade to the latest version.</p>
        <div class="btn-group">
            <button class="btn btn-success"><?php echo $pageL->get('btn_upgrade') ?></button>
        </div>
    </div>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>system-repair-upgrade.js"></script>
