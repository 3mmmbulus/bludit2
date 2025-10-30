<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>sites-overview.css">

<div class="sites-overview">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <table>
        <thead>
            <tr>
                <th><?php echo $pageL->get('col_site_name') ?></th>
                <th><?php echo $pageL->get('col_domain') ?></th>
                <th><?php echo $pageL->get('col_status') ?></th>
                <th><?php echo $pageL->get('col_actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <!-- Sites list will be populated here -->
        </tbody>
    </table>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>sites-overview.js"></script>
