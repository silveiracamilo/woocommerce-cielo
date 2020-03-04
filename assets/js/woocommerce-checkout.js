jQuery(function(g){
    let $checkout_form =  g("form.checkout");
    $checkout_form.on("click", 'input[name="payment_method"]', payment_method_selected);

    function payment_method_selected(e) {
        if (e.stopPropagation(),
        1 < g(".payment_methods input.input-radio").length) {
            var t = g("div.payment_box." + g(this).attr("ID"))
                , o = g(this).is(":checked");
            o && !t.is(":visible") && (g("div.payment_box").filter(":visible").slideUp(230),
            o && t.slideDown(230))
        } else
            g("div.payment_box").show();
    }

    g('#billing_phone').focusout(function(){
		var phone, element;
		element = g(this);
		element.unmask();
		phone = element.val().replace(/\D/g, '');
		if(phone.length > 10) {
			element.mask("(99) 99999-9999");
		} else {
			element.mask("(99) 9999-99999");
		}
	}).trigger('focusout');
    
    g('#billing_postcode').mask('00000-000');
    g('#cielo-card-number, #cielo-debit-card-number').mask('0000.0000.0000.0000');
    g('#cielo-card-expiry, #cielo-debit-card-expiry').mask('00/0000');
    g('#cielo-card-cvv, #cielo-debit-card-cvv').mask('0000');
    setTimeout(function(){
        console.log("add on keyuop");
        g('#cielo-card-number, #cielo-debit-card-number').keyup(function() {
            console.log("keyup");
            var b = brand.getCardFlag(g(this).val());
    
            g(this).parent().find(".brand").val(b);
            if(b!=false) {
                var url = "/wp-content/themes/vw-mobile-app-child/images/bandeiras/"+b.toLowerCase()+".png";
                g(this).css("background-size", "auto");
                g(this).css("background-image", "url('"+url+"')");
            } else {
                // g(this).parent().find("label").find(".bandeira_img").attr("src","");
                g(this).css("background-image","url('')");
            }
        });
    }, 1000);    
});

var brand = {

    /**
    * getCardFlag
    * Return card flag by number
    *
    * @param cardnumber
    */
    getCardFlag: function(cardnumber) {
        var cardnumber = cardnumber.replace(/[^0-9]+/g, '');

        var cards = {
            Visa      : /^4[0-9]{12}(?:[0-9]{3})/,
            Master : /^5[1-5][0-9]{14}/,
            Diners    : /^3(?:0[0-5]|[68][0-9])[0-9]{11}/,
            Amex      : /^3[47][0-9]{13}/,
            Discover  : /^6(?:011|5[0-9]{2})[0-9]{12}/,
            Hipercard  : /^(606282\d{10}(\d{3})?)|(3841\d{15})/,
            Elo        : /^((((636368)|(438935)|(504175)|(451416)|(636297))\d{0,10})|((5067)|(4576)|(4011))\d{0,12})/,
            JCB        : /^(?:2131|1800|35\d{3})\d{11}/,
            Aura      : /^(5078\d{2})(\d{2})(\d{11})$/
        };

        for (var flag in cards) {
            if(cards[flag].test(cardnumber)) {
                return flag;
            }
        }

        return false;
    }

}