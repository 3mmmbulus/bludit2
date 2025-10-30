<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>spider-logs.css">

<div class="spider-logs">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <table>
        <thead>
            <tr>
                <th><?php echo $pageL->get('col_date') ?></th>
                <th><?php echo $pageL->get('col_ip') ?></th>
                <th><?php echo $pageL->get('col_user_agent') ?></th>
                <th><?php echo $pageL->get('col_url') ?></th>
            </tr>
        </thead>
        <tbody>
            <!-- Spider logs will be displayed here -->
        </tbody>
    </table>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>spider-logs.js"></script>
