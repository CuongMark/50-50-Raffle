    document.observe('dom:loaded',function(){
        $$('.rafflee-index-index .product-info .actions button span span').each(function(el){
            el.update('Purchase Tickets');
        });
    });