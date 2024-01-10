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
            this.deleteItem();
            this.editItem();
        }

        editItem(){
            parent.on('click', 'a.transfer-visitor-edit', function(e){
                e.preventDefault();
                let _this  = $(this);
                let id     = _this.data('id');
                let name   = _this.closest('td').children('a').text();
                let oldUrl = _this.closest('td').siblings('td.old_url').find('a').text();
                let newUrl = _this.closest('td').siblings('td.new_url').find('a').text();
                
                _this.parents('body').append(`
                    <div class="edit-item-${id}" title="Edit item - ${name}">
                        <p>
                            <label>Name:
                                <input type="text" id="name-${id}" class="widefat">
                            </label>
                        </p>
                        <p>
                            <label>Old Url:
                                <input type="url" id="old-url-${id}" class="widefat">
                            </label>
                        </p>
                        <p>
                            <label>New Url:
                                <input type="url" id="new-url-${id}" class="widefat">
                            </label>
                        </p>
                    </div>
                `);

                $(`.edit-item-${id}`).dialog({
                    resizable: false,
                    height: "auto",
                    width: 400,
                    modal: true,
                    buttons: {
                      "Save Changes": function() {
                        $( this ).dialog( "close" );
                        
                        // do something api request to delete
                        $(`.edit-item-${id}`).remove();

                      },
                      Cancel: function() {
                        $( this ).dialog( "close" );
                        $(`.edit-item-${id}`).remove();
                      }
                    }
                });

            });
        }
        
        deleteItem(){
            parent.on('click', 'a.transfer-visitor-delete', function(e){
                e.preventDefault();
                let message = 'if click delete button then it will delete permanently.';
                let id = $(this).data('id');
                $(this).parents('body').append(`
                    <div class="dialog-${id}" title="Are you sure to delete?">
                        <p>${message}</p>
                    </div>
                `);

                $(`.dialog-${id}`).dialog({
                    resizable: false,
                    height: "auto",
                    width: 300,
                    modal: true,
                    buttons: {
                      "Yes": function() {
                        $( this ).dialog( "close" );
                        
                        // do something api request to delete
                        $(`.dialog-${id}`).remove();

                      },
                      Cancel: function() {
                        $( this ).dialog( "close" );
                        $(`.dialog-${id}`).remove();
                      }
                    }
                });

                setTimeout( ()=>{
                    $('#transferDelete').remove();
                }, ( 1000 * 60 ) );
            })
        }

        insertItem(){
            parent.on('submit', 'form.form-add-new-record', function(e){
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