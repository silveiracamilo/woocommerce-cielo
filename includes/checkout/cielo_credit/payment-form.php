<?php
/**
 * Cielo Credit Card - checkout form.
 * Author Camilo da Silveira
 * Site silveiracamilo.com.br
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset id="cielo-credit-payment-form" class="cielo-payment-form">
	<p class="form-row form-row-first">
		<label for="cielo-card-number"><?php _e( 'Card Number', 'cielo-woocommerce' ); ?> <span class="required">*</span><img src="" class="bandeira_img"></label>
		<input id="cielo-card-number" name="credit_card_number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="22" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
		<input class="brand" name="credit_card_brand" type="hidden" />
	</p>
	<p class="form-row form-row-last">
		<label for="cielo-card-holder-name"><?php _e( 'Name Printed on the Card', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-card-holder-name" name="credit_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="cielo-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-card-expiry" name="credit_card_expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'cielo-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="cielo-card-cvc"><?php _e( 'Security Code', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-card-cvv" name="credit_card_cvc" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="<?php _e( 'CVV', 'cielo-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<?php if ( ! empty( $installments ) ) : ?>
		<p class="form-row form-row-wide">
			<label for="cielo-installments"><?php _e( 'Installments', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
			<?php echo $installments; ?>
		</p>
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>