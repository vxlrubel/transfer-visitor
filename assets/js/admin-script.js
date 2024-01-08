;(function($){
    'use strict';
    const doc     = $(document);
    const nonce   = redirections.nonce;
    const apiUrl  = redirections.rest_url;
    const ajaxUrl = redirections.ajax_url;
    const parent  = $('[class*="transfer-visitor-"]');
    class TransferVisitor{
        constructor(){
            this.insertItem();
        }

        insertItem(){
            parent.on('submit', 'form', function(e){
                e.preventDefault();
                let _this         = $(this);
                let parentElement = $(this).closest('.wrap');
                let name          = _this.find('#redirection-name');
                let old_url       = _this.find('#old-url');
                let new_url       = _this.find('#new-url');
                let data          = {
                    name   : name.val(),
                    old_url: old_url.val(),
                    new_url: new_url.val()
                }

                let headers = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }

                $.ajax({
                    type      : 'POST',
                    url       : apiUrl,
                    data      : JSON.stringify( data ),
                    headers   : headers,
                    beforeSend: ()=>{
                        parentElement
                            .find('input.add-new-record')
                            .after('<span class="transfer-spin"></span>');
                    },
                    success   : (res)=>{
                        let spin = parent.find('span.transfer-spin');
                        spin.fadeOut(300, ()=>{spin.remove()});
                        parentElement
                            .find('input.add-new-record')
                            .after(`<span class="transfer-result success">${res}</span>`);

                        name.val('');
                        old_url.val('');
                        new_url.val('');
                        
                        setTimeout(() => {
                            $('.transfer-result.success').fadeOut(300, ()=>{
                                $('.transfer-result.success').remove();
                            })
                        }, 1000);
                    },
                    error     : ()=>{
                        parentElement
                            .find('input.add-new-record')
                            .after(`<span class="transfer-result success">Something went wrong</span>`);
                    }
                });

                
            });
        }
    }

    doc.ready(()=>{ new TransferVisitor() });

})(jQuery);