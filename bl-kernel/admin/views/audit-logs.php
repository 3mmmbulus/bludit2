<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>audit-logs.css">

<div class="audit-logs">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <table>
        <thead>
            <tr>
                <th><?php echo $pageL->get('col_date') ?></th>
                <th><?php echo $pageL->get('col_user') ?></th>
                <th><?php echo $pageL->get('col_action') ?></th>
                <th><?php echo $pageL->get('col_details') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($syslog->get() as $entry): ?>
            <tr>
                <td><?php echo isset($entry['date']) ? $entry['date'] : '' ?></td>
                <td><?php echo isset($entry['username']) ? $entry['username'] : '' ?></td>
                <td><span class="action-badge"><?php echo isset($entry['type']) ? $entry['type'] : '' ?></span></td>
                <td><?php echo isset($entry['description']) ? $entry['description'] : '' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>audit-logs.js"></script>
