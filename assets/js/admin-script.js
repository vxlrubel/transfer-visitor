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
            this.openEditPopup();
            this.editItemFromPopup();
            this.closePopup();
            this.changePopupName();
            this.deleteMultipleItems();
            this.moveToTrash();
            this.restoreItemFromTrash();
        }

        restoreItemFromTrash(){
            parent.on('click', 'a.submit-restore', function(e){
                e.preventDefault();
                let _this      = $(this);
                let id         = parseInt(_this.attr('id'));
                let restoreUrl = `${apiUrl}/restore/${id}`;

                let headers = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }
                let data    = {
                    id : id
                }

                $.ajax({
                    type      : 'PATCH',
                    url       : restoreUrl,
                    data      : JSON.stringify(data),
                    headers   : headers,
                    beforeSend: ()=>{
                        _this.text('Publishing');
                    },
                    success   : (response)=>{
                        console.log(response)
                        if ( response ){
                            let row = _this.closest('tr');
                            row.addClass('remove-from-list');
                            row.fadeOut(300, ()=>{
                                row.remove();
                            });
                        }
                    },
                    error     : (error)=>{
                        if( error ){
                            alert('Something went wrong.');
                        }
                    }
                });
                

            });
        }

        moveToTrash(){
            parent.on('click', '.submit-trush', function(e){
                e.preventDefault();
                let _this   = $(this);
                let id      = parseInt(_this.attr('id'));
                let trashUrl= apiUrl + '/' + 'trash'  + '/' + id;
                let data    = {
                    id : id
                }
                let headers = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }

                $.ajax({
                    type      : 'PATCH',
                    url       : trashUrl,
                    data      : JSON.stringify(data),
                    headers   : headers,
                    beforeSend: ()=>{
                        _this.text('Move to trash');
                    },
                    success   : (response)=>{
                        if ( response ){
                            let row = _this.closest('tr');
                            row.addClass('remove-from-list');
                            row.fadeOut(300, ()=>{
                                row.remove();
                            });
                        }
                    },
                    error     : (error)=>{
                        if( error ){
                            alert('Something went wrong.');
                        }
                    }
                });
            })
        }

        deleteMultipleItems(){
            parent.on('click', 'input#doaction[type="submit"]', function(e){
                e.preventDefault();
                let _this       = $(this);
                let selfText    = _this.val();
                let selectValue = _this.siblings('select#bulk-action-selector-top').val();
                let items       = _this.closest('.wrap').find('input[name="transfer_visitor[]"]:checked');
                let itemsValue  = items.map(
                    function(){
                        return this.value;
                    }
                ).get();

                if ( 'delete' !== selectValue ){
                    return;
                }

                if ( itemsValue === 0 ){
                    return;
                }

                let multipleDeleteUrl = apiUrl + '/' + 'drop-items';
                let headers = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }

                let data = {
                    ids: itemsValue
                }

                $.ajax({
                    type      : 'DELETE',
                    url       : multipleDeleteUrl,
                    data      : JSON.stringify( data ),
                    headers   : headers,
                    beforeSend: ()=>{
                        _this.val('Deleting...');
                    },
                    success   : (response)=>{
                        if ( response ){
                            _this.val(selfText);
                            items.closest('tr').addClass('remove-from-list').fadeOut(300, ()=>{
                                items.closest('tr').remove();
                            });
                        }
                    },
                    error     : (error)=>{
                        alert('Something went wrong');
                    }
                });
            });
        }

        changePopupName(){
            parent.on('keyup', '#change-name', function(e){
                e.preventDefault();
                let _this   = $(this);
                let getName = _this.val();
                let field   = _this.closest('.popup-body').siblings('.popup-header').find('span.get-name');
                field.html(getName);
            });
        }

        openEditPopup(){
            parent.on('click', 'a.transfer-visitor-edit', function(e){
                e.preventDefault();
                let _this  = $(this);
                let id     = _this.data('id');
                let name   = _this.closest('td').children('a').text();
                let oldUrl = _this.closest('td').siblings('td.old_url').find('a').text();
                let newUrl = _this.closest('td').siblings('td.new_url').find('a').text();
                
                _this.closest('.wrap').append(`
                    <form class="transfer-popup edit-item-${id}" action="javascript:void(0)">
                        <div class="popup-header">
                            <h2>Edit item - <span class="get-name">${name}</span></h2>
                            <span data-transfer-dismisable="true"><span class="dashicons dashicons-no-alt"></span></span>
                        </div>
                        <div class="popup-body">
                            <p>
                                <label>Name:
                                    <input type="text" value="${name}" class="widefat" id="change-name">
                                </label>
                            </p>
                            <p>
                                <label>Old Url:
                                    <input type="url" id="old-url" value="${oldUrl}" class="widefat">
                                </label>
                            </p>
                            <p>
                                <label>New Url:
                                    <input type="url" id="new-url" value="${newUrl}" class="widefat">
                                </label>
                            </p>
                        </div>
                        <div class="popup-footer">
                            <input type="submit" value="Save Changes">
                            <input type="hidden" id="submit-id" value="${id}">
                            <button type="button" data-transfer-dismisable="true">No</button>
                        </div>
                    </form>
                `);
            });
        }

        editItemFromPopup(){
            parent.on('submit', 'form.transfer-popup', function(e){
                e.preventDefault();
                let _this = $(this);
                let submitButton = _this.find('input[type="submit"]');
                let buttonText = submitButton.val();
                let id = _this.find('input#submit-id').val();
                let editUrl = apiUrl + '/' + id;

                let data = {
                    id     : parseInt(id),
                    name   : _this.find('input#change-name').val(),
                    old_url: _this.find('input#old-url').val(),
                    new_url: _this.find('input#new-url').val(),
                }

                let headers = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }

                $.ajax({
                    type      : 'POST',
                    url       : editUrl,
                    data      : JSON.stringify(data),
                    headers   : headers,
                    beforeSend: ()=>{
                        submitButton.val('Updating...');
                    },
                    success   : (response)=>{
                        if ( response ){
                            submitButton.val( buttonText );
                            _this.find('.popup-body').find( `.successful-notice` ).remove();
                            _this.find('.popup-body').append( `<div class="popup-notice success">${response}</div>` );
                            _this.closest('.wrap').find(`a[data-ajax-name="${id}"]`).text( data.name );
                            _this.closest('.wrap').find(`a[data-ajax-old-url="${id}"]`).text( data.old_url );
                            _this.closest('.wrap').find(`a[data-ajax-old-url="${id}"]`).attr( 'href', data.old_url );
                            _this.closest('.wrap').find(`a[data-ajax-new-url="${id}"]`).text( data.new_url );
                            _this.closest('.wrap').find(`a[data-ajax-new-url="${id}"]`).attr( 'href', data.new_url );
                        }
                        
                    },
                    error     : (error)=>{
                        if (error){
                            _this.find('.popup-body').find( `.successful-notice` ).remove();
                            _this.find('.popup-body').append( `<div class="popup-notice warning">Something went wrong.</div>` )
                        }
                    }
                });

            });
        }
        
        deleteItem(){
            // open delete popup
            parent.on('click', 'a.transfer-visitor-delete', function(e){
                e.preventDefault();
                let _this = $(this);
                let id    = _this.data('id');
                _this.closest('.wrap').append(`
                    <div class="transfer-popup">
                        <div class="popup-header">
                            <h2>Are you sure?</h2>
                            <span data-transfer-dismisable="true"><span class="dashicons dashicons-no-alt"></span></span>
                        </div>
                        <div class="popup-body">
                            <span>
                                If you click yes then the item will be delete permanently.
                            </span>
                        </div>
                        <div class="popup-footer">
                            <button type="button" data-delete-item id="${id}">Yes</button>
                            <button type="button" data-transfer-dismisable="true">No</button>
                        </div>
                    </div>
                `);
            });
            
            // delete item
            parent.on('click', 'button[data-delete-item]', function(e){
                e.preventDefault();
                let _this     = $(this);
                let id        = _this.attr('id');
                let popupBox  = _this.closest('.transfer-popup');
                let deleteUrl = apiUrl + '/' + id;
                let itemRow   = parent.find(`a[data-ajax-name="${id}"]`).closest('tr');
                let data      = {
                    id: parseInt( id )
                }
                let headers   = {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce'  : nonce
                }

                $.ajax({
                    type      : 'DELETE',
                    url       : deleteUrl,
                    data      : JSON.stringify(data),
                    headers   : headers,
                    beforeSend: ()=>{
                        _this.text('Deleting...');
                    },
                    success   : (response)=>{
                        if ( response ){
                            popupBox.fadeOut(100, ()=>{
                                popupBox.remove();
                            });

                            itemRow.addClass('remove-from-list').fadeOut(300, ()=>{
                                itemRow.remove();
                            })
                        }
                    },
                    error     : (error)=>{
                        alert('Something went wrong.')
                    }
                });
                
                
                
            });


        }

        closePopup(){
            parent.on('click', '[data-transfer-dismisable="true"]', function(e){
                e.preventDefault();
                let _popup = $(this).closest('.transfer-popup');
                _popup.fadeOut(300, ()=>{
                    _popup.remove();
                })
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