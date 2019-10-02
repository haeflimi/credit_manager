<?php
$fh = Core::make('helper/form');?>
<div id="cm-add-record" class="ccm-ui">
    <form action="<?= $this->action('confirm') ?>" method="POST">
        <div class="form-group">
            <label><?= t('Add/ Subtract Value') ?></label>
            <?=$fh->number('recordValue', ['class'=>'form-control'])?>
        </div>
        <div class="form-group">
            <label for="comment"><?= t('Comment') ?></label>
            <?=$fh->text('recordComment', ['class'=>'form-control'])?>
        </div>
        <input type="hidden" value="<?=Core::make('token')->generate('addRecord');?>" name="ccm_token">
        <input type="hidden" value="<?=$uId;?>" name="recordUid">
    </form>
    <div class="dialog-buttons">
        <button class="btn btn-success pull-left" onclick="$('#cm-add-record form').submit()"><?=t('Confirm')?></button>
        <button class="btn btn-danger pull-right"  onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
    </div>
</div>
