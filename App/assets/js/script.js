jQuery(document).ready(function($){
    let selected_product=[];
    let remove_products = []; 
    $('#bxgx_product_form').on('submit', function(e){
        e.preventDefault();
        selected_product=[];
        $(".bxgx-product").each(function(){
            const ProductId = $(this).data('product-id');
            selected_product.push(ProductId);
            
        });

        $.ajax({
            url:bxgxScript.ajax_url,
            type:"POST",
            dataType:"json",
            data:{
                action:'bxgx_save_selected_products',
                data:selected_product,
                nonce:bxgxScript.nonce,
            },
            success:function(response){
                if(response.success){
                    showToast(response.data.message,'sucess');
                }
              
            },
            error:function(error){
                if(response.error){
                    showToast(response.data.message);
                }
            }
        })
    })

    $('#bxgx_product_search').on('keyup', function(){
        const searchQuery= $(this).val();
        
        //ajax request
        $.ajax({
            url:bxgxScript.ajax_url,
            type:'POST',
            dataType:'json',
            data:{
                action:'bxgx_search_products',
                query:searchQuery,
                nonce:bxgxScript.nonce,
            },
            success:function(response){
                $("#bxgx_product_dropdown").empty().show();
                if(response.length > 0){
                    response.forEach(function(product){
                        $("#bxgx_product_dropdown").append(
                           '<li class="list-group-item" data-product-id='+product.id+'>'+product.name+'</li>'
                        )
                    });
                }else{
                    $("#bxgx_product_dropdown").html('<li class="list-group-item">No Products Found...</li>')
                }
            }
        })
    });
    
    //dropdown listing
    $(document).on('click', '#bxgx_product_dropdown .list-group-item', function(){
        const ProductId= $(this).data('product-id');
        const ProductName= $(this).text();
        
        if($('.bxgx-product[data-product-id="'+ProductId+'"]').length===0){
            $('#selected_products').append(
                '<span class="bxgx-product" data-product-id="' + ProductId + '">' +
                ProductName + ' <a href="#" class="bxgx-remove-product">x</a></span>'
            );
            
        }
        $('#bxgx_product_search').val('');
        $('#bxgx_product_dropdown').empty();
    })
    
    //remove product

    $(document).on('click', '.bxgx-remove-product', function(e){
        e.preventDefault();

        const ProductId=$(this).parent().data('product-id');
        if (!remove_products.includes(ProductId)) {
            remove_products.push(ProductId);
        }
        $(this).parent().remove();

        $.ajax({
            url:bxgxScript.ajax_url,
            type:'POST',
            dataType:'json',
            data:{
                action:'bxgx_remove_products',
                product_id:ProductId,
                nonce:bxgxScript.nonce,
            },
            success:function(response){
                if(response.success){
                    showToast(response.data.message,'error');
                }
            },
            error:function(error){
                alert('failed to delete');
            }
        })
    })
    $(document).on('click', function(e){
        if (!$(e.target).closest('#bxgx_product_form').length) {
            $('#bxgx_product_dropdown').hide();
        }
    })
})

//toast
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;

    container.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 3000);
}