<?php defined('BLUDIT') or die('Bludit CMS.'); ?>

<link rel="stylesheet" href="<?php echo DOMAIN_CORE_CSS ?>content-management.css">

<div class="content-management">
    <h2><?php echo $pageL->get('title') ?></h2>
    
    <div class="toolbar">
        <button class="btn btn-primary"><?php echo $pageL->get('btn_new') ?></button>
    </div>
    
    <table>
        <thead>
            <tr>
                <th><?php echo $pageL->get('col_title') ?></th>
                <th><?php echo $pageL->get('col_status') ?></th>
                <th><?php echo $pageL->get('col_date') ?></th>
                <th><?php echo $pageL->get('col_actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pages->getList(1, -1) as $page): ?>
            <tr>
                <td><?php echo $page->title() ?></td>
                <td><?php echo $page->published() ? 'Published' : 'Draft' ?></td>
                <td><?php echo $page->date() ?></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm"><?php echo $pageL->get('btn_edit') ?></button>
                        <button class="btn btn-sm btn-danger"><?php echo $pageL->get('btn_delete') ?></button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="<?php echo DOMAIN_CORE_JS ?>content-management.js"></script>
