<?php
$fh = Core::make('helper/form');?>
<div id="cm-history" class="ccm-ui ccm-dialog-content">
    <table class="ccm-search-results-table">
        <thead>
            <tr>
                <th><a><?=t('Date/ Time')?></a></th>
                <th><a><?=t('Comment')?></a></th>
                <th><a><?=t('Value')?></a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($history as $record):?>
                <tr>
                    <td><?=$record->getTimestamp()->format('d.m.Y H:i')?></td>
                    <td><?=$record->getComment()?></td>
                    <td><?=$record->getValue()?></td>
                </tr>
            <?php endforeach;?>
        </tbody>

    </table>
</div>