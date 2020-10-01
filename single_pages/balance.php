<?php
defined('C5_EXECUTE') or die("Access Denied.");
$c = Page::getCurrentPage();
$p = new Permissions($c);
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$this->inc('elements/header_top.php');?>

<section id="comp-balance" class="container">
    <main>
        <div class="row">
            <div class="col-md-4 col-sm-8 col-xs-6">
                <div class="box">
                    <div class="description">
                        Dein Guthaben
                    </div>
                    <div class="big-text <?=($balance>=0)?'text-success':'text-danger'?>">
                        <?= round($balance,3) ?> SFr.
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 col-sm-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col"><?=t('Date/ Time')?></th>
                        <th scope="col"><?=t('Comment')?></th>
                        <th scope="col"><?=t('Category')?></th>
                        <th scope="col"><?=t('Value')?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($history as $record):?>
                        <tr>
                            <td><?=$record->getTimestamp()->format('d.m.Y H:i')?></td>
                            <td><?=$record->getComment()?></td>
                            <td><?php foreach($record->getCategories() as $crc){
                                    echo '<span class="badge badge-primary pr-2" data-nodeid="'.$crc->getCategoryId().'">'.$crc->getCategoryName().'</span>';
                            }?></td>
                            <td><?=$record->getValue()?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if($count > $limit):?>
                        <tr>
                            <td>...</td>
                            <td>...</td>
                            <td>...</td>
                        </tr>
                    <?php endif;?>
                    </tbody>

                </table>
            </div>
        </div>
    </main>
</section>