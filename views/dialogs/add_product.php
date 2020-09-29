<?php
$fh = Core::make('helper/form');?>
<div id="cm-add-product" class="ccm-ui">
    <form action="<?= $this->action('confirm') ?>" method="POST">
        <div class="form-group">
            <label><?= t('Produktname') ?></label>
            <?=$fh->text('name',$name, ['class'=>'form-control'])?>
        </div>
        <div class="form-group">
            <label><?= t('Preis') ?></label>
            <?=$fh->number('price',$price, ['class'=>'form-control'])?>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="isSelfService" value="1" <?=$isSelfService?'checked':''?>> <?= t('Self-Service Item') ?></label>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="isOrder" value="1" <?=$isOrder?'checked':''?>> <?= t('Order Item') ?></label>
        </div>
        <div class="form-group">
            <label for="category"><?= t('Categories') ?></label>
            <?php echo $fh->selectMultiple('selectedCategories', $categoryTreeNodes, $selectedCategories,['style'=>'padding: 0;']) ?>
            <?php if (0 && is_object($categoryTree)) :?>
                <div data-tree="<?=$categoryTree->getTreeID(); ?>"></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="category"><?= t('Image') ?></label>
            <?php echo $fm->image('image', 'image', t('Choose Image'), $image); ?>
        </div>
        <input type="hidden" value="<?=Core::make('token')->generate('addProduct');?>" name="ccm_token">
        <input type="hidden" value="<?=$pId;?>" name="pId">
    </form>
    <div class="dialog-buttons">
        <button class="btn btn-success pull-left" onclick="$('#cm-add-product form').submit()"><?=t('Confirm')?></button>
        <button class="btn btn-danger pull-right"  onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
    </div>
</div>
<?php if(is_object($categoryTree)): ?>
<script type="text/javascript">
    $(function() {
        $('select[name=topicTreeIDSelect]').on('change', function() {
            window.location.href = '<?=$view->url('/dashboard/system/attributes/topics', 'view'); ?>' + $(this).val();
        });

        $('[data-tree]').concreteTree({
            'treeID': '<?=$categoryTree->getTreeID(); ?>'
        });

        $("#selectedCategories").select2();
    });
</script>
<?php endif; ?>