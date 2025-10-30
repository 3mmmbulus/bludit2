<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>cache-list.css">

<div class="cache-list">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <div class="toolbar">
        <form method="post">
            <button type="submit" name="clear" class="btn btn-danger"><?php echo $pageL->get('btn_clear') ?></button>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th><?php echo $pageL->get('col_key') ?></th>
                <th><?php echo $pageL->get('col_size') ?></th>
                <th><?php echo $pageL->get('col_date') ?></th>
            </tr>
        </thead>
        <tbody>
            <!-- Cache items will be displayed here -->
        </tbody>
    </table>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>cache-list.js"></script>
