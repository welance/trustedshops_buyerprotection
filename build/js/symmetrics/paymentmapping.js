document.observe('dom:loaded', function(){
    var paypalNotice = $$('.paypal-payment-notice').first();
    if (paypalNotice) {
        var fieldTemplate = '';

        $$('.main-col-inner #config_edit_form fieldset.config').each(function(sectionNode) {
            var tableNode = sectionNode.select('table.form-list tbody');
            var firstField = tableNode.select('tr').first();
            var sectionId = sectionNode.identify();
            var newField = fieldTemplate;
            newField = newField.replace(/\%code\%/, sectionId);
            newField = newField.replace(/\%options\%/, '');
            if (firstField) {
                firstField.insert({
                    after: newField
                });
            }
        })
    }
})