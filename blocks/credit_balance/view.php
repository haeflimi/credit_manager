<?php
defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

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
    <div class="col-md-7 col-md-offset-1 col-sm-8 col-xs-12">

        <h4>Ausgleichen per Paypal</h4>

        <?php if($balance<0):?>

            <div class="row">
                <div class="col-xs-12 col-md-4">
                    <div id="paypal-button-container"></div>
                    <div id="paypal-success" class="alert alert-success" style="display: none">
                        <p><strong>Zahlung erfolgreich.</strong><br/>
                            Unter Umständen kann es einige Minuten dauern, bis der Kontostand auf unserer Homepage korrekt angezeigt wird. - Bitte die Zahlung NICHT wiederholen
                            und bei Unstimmigkeiten bie <a href="mailto:tuborg@turicane.ch">TuBorg</a> melden.</p>
                    </div>
                </div>
            </div>

            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?=$paypalTotal?>'
                                },
                                custom_id: '<?=$paypalPayload?>'
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            $('#paypal-success').fadeIn('slow');

                            return fetch('/ccm/credit_manager/callback/verify', {
                                method: 'post',
                                headers: {
                                    'content-type': 'application/json'
                                },
                                body: JSON.stringify({
                                    orderID: data.orderID
                                })
                            });

                        });
                    }
                }).render('#paypal-button-container');
            </script>

        <?php else:?>
            <p>
                Die Überweisung per Paypal ist nur bei einem negativen Kontostand möglich.
            </p>
        <?php endif?>
    </div>
</div>

