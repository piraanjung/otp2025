<div id="inventoryScreen" class="iss-hidden">
        <iframe src=""  style=" width: 100%; height: 800px; border: none;"
            id="inventoryIframe">
        </iframe>
    </div>

<script>
    function manageInventoryIframe(status){
        console.log('manageInventoryIframe')
        const $iframe = $('#inventoryIframe');
        let url = '';
        if(status === 'open'){
            url = '/inventory/items/iframe'
        }
    
        $iframe.attr('src', url);
    }
manageInventoryIframe('open')
</script>