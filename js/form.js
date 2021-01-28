(function($) {
    "use strict";

    var clFromFormat = function() {
        jQuery("#categoria").change(function(){
            var select_val = this.value.split("-");
            var id_show = parseInt(select_val[0]);
            jQuery(".elements-to-show").each(function(){
                var canShow = JSON.parse(jQuery(this).attr("showin"));
                if(jQuery.inArray( id_show, canShow ) >= 0)
                    jQuery(this).show();
                else
                    jQuery(this).hide();
            });
        });
    };

    var clSendForm = function(){
        jQuery("#formObra").submit(function( event ){
            event.preventDefault();
            var formData = new FormData(this);
            var btn_enviar = jQuery("#btn_enviar");
            var status_msg = jQuery("#status_msg");
            var loading = jQuery("#loading");
            btn_enviar.find("span").text("Enviando...");
            btn_enviar.attr("disabled", "disabled");
            loading.show();
            setTimeout(function(){
                jQuery.ajax({
                    method: "POST",
                    url: "contact/contact.php?form_send=s",
                    data: formData,
                    async: false,
                    cache: false,
                    contentType: false,
                    processData: false,
                    //dataType: "json",
                    success: function (data) {
                        var resp = JSON.parse(data);
                        btn_enviar.find("span").text("Gracias por su envío");
                        status_msg.slideDown();
                        status_msg.text(resp.success);
                        loading.fadeOut('slow', function(){
                             setTimeout(function(){jQuery('#status_msg').slideUp()}, 5000);
                        });
                        console.log(resp);
                    },
                    error: function (data) {
                        var resp = JSON.parse(data);
                        btn_enviar.prop("disabled", false);
                        btn_enviar.find("span").text("Reenviar!");
                        loading.hide();
                        status_msg.text(resp.error);
                        console.log(resp);
                    }
                });
            }, 500);
        });
    };

    (function clInit() {
        clFromFormat();
        clSendForm();
        jQuery(".elements-to-show").hide();
    })();

})(jQuery);