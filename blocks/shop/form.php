<?php
defined('C5_EXECUTE') or die("Access Denied.");
$fh = Core::make('helper/form');
?>
<div class="form-group">
    <div class="row">
        <label class="control-label col-sm-2">
            <?=t('Nächstes Catering')?>:
        </label>
        <div class="col-sm-10">
            <?=$fh->text('run_time', $run_time)?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label class="control-label col-sm-2">
            <?=t('Active Category')?>:
        </label>
        <div class="col-sm-10">
            <?php echo $fh->select('active_category', $categoryTreeNodes, (integer)$active_category,['style'=>'padding: 0;']) ?>
        </div>
    </div>
</div>