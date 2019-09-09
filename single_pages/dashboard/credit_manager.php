<?php defined('C5_EXECUTE') or die("Access Denied.");
$nh = Core::make('helper/navigation') ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <td><label>Nickname</label></td>
            <td><label>E-Mail</label></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($userList as $user):
            $ui = $user->getUserInfoObject(); ?>
            <tr>
                <td>
                    <a href="<?= URL::to('/dashboard/users/search/view/', $user->getUserID()) ?>"><?= $user->getUserName() ?></a>
                </td>
                <td><?= $user->getUserEmail() ?></td>
                <td>
                    <div data-container="editable-fields">
                    <?php
                    $akv = $user->getAttributeValueObject('tgc_balance');
                    if (is_object($akv)):
                        $ak = $akv->getAttributeKey();
                        $display = $akv->getDisplayValue();?>

                        <div class="editable-attribute-wrapper">
                            <div class="col-md-3">
                                <p class="editable-attribute-display-name"><?= $ak->getAttributeKeyDisplayName() ?></p>
                            </div>
                            <div class="col-md-9" data-editable-field-inline-commands="true">
                                <div class="editable-attribute-field-inline">
                                    <ul class="ccm-edit-mode-inline-commands">
                                        <li>
                                            <a href="#" data-key-id="<?= $ak->getAttributeKeyID() ?>" data-url="<?= $view->action('clear_attribute', $user->getUserID()) ?>" data-editable-field-command="clear_attribute">
                                                <i class="fa fa-trash-o"></i>
                                            </a>
                                        </li>
                                    </ul>
                                    <span data-title="<?= $ak->getAttributeKeyDisplayName() ?>"
                                            data-key-id="<?= $ak->getAttributeKeyID() ?>-<?=$user->getUserID()?>"
                                            data-name="<?= $ak->getAttributeKeyID() ?>"
                                            data-editable-field-type="xeditableAttribute"
                                            data-url="<?= $view->action('update_attribute', $user->getUserID()) ?>"
                                            data-type="concreteattribute"
                                        <?php echo $ak->getAttributeTypeHandle() === 'textarea' ? "data-editableMode='inline'" : ''; ?>
                                    ><?= $display ?></span>
                                </div>
                            </div>
                        </div>
                        <div style="display: none">
                            <div data-editable-attribute-key-id="<?= $ak->getAttributeKeyID() ?>-<?=$user->getUserID()?>">
                                <?php
                                $value = $akv;
                                $ak->render(new \Concrete\Core\Attribute\Context\DashboardFormContext(), $value);
                                ?>
                            </div>
                        </div>

                    <?php
                    endif;
                    ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<script type="text/javascript">
    $(function () {
        $('div[data-container=editable-fields]').concreteEditableFieldContainer({
            url: '<?=$view->action('save', $user->getUserID())?>',
            data: {
                ccm_token: '<?=$token->generate()?>'
            }
        });
    });
</script>