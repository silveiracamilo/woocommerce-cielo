<?php
/**
 * Cielo Debit Card - checkout form.
 * Author Camilo da Silveira
 * Site silveiracamilo.com.br
 *
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<fieldset id="cielo-debit-payment-form" class="cielo-payment-form">
	<p class="form-row form-row-first">
		<label for="cielo-debit-card-number"><?php _e( 'Card Number', 'cielo-woocommerce' ); ?> <span class="required">*</span><img src="" class="bandeira_img"></label>
		<input id="cielo-debit-card-number" name="debit_card_number" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="22" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
		<input class="brand" name="debit_card_brand" type="hidden" />
	</p>
	<p class="form-row form-row-last">
		<label for="cielo-debit-card-holder-name"><?php _e( 'Name Printed on the Card', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-debit-card-holder-name" name="debit_card_holder_name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="cielo-debit-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-debit-card-expiry" name="debit_card_expiry" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'cielo-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="cielo-debit-card-cvc"><?php _e( 'Security Code', 'cielo-woocommerce' ); ?> <span class="required">*</span></label>
		<input id="cielo-debit-card-cvv" name="debit_card_cvc" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="<?php _e( 'CVV', 'cielo-woocommerce' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<?php if ( 0 < $discount ) : ?>
		<p class="form-row form-row-wide">
			<?php printf( __( 'Payment by debit have discount of %s. Order Total: %s.', 'cielo-woocommerce' ), $discount . '%', sanitize_text_field( woocommerce_price( $discount_total ) ) ); ?>
		</p>
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>