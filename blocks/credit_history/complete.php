<?php
defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<div class="row">
    <div class="col-xs-12 col-sm-12">
        <table class="table table-striped">
            <thead>
            <tr>
                <th scope="col"><?=t('Date/ Time')?></th>
                <th scope="col"><?=t('Comment')?></th>
                <th scope="col"><?=t('Value')?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($history as $record):?>
                <tr>
                    <td><?=$record->getTimestamp()->format('d.m.Y H:i')?></td>
                    <td><?=$record->getComment()?></td>
                    <td><?=$record->getValue()?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
