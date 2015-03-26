jQuery(document).ready(function($){
    
    $(document).on('submit', '.gmema-widget form', function(){
        var $this = $(this), 
            $parent = $this.closest('.gmema-widget');
        
        if (!$this.data('uid')) {
            return false;
        }
        
        var ajaxurl = $parent.data('ajaxurl');
        var data = $this.serialize() + '&action=gmema_subscribe&uid='+$this.data('uid');
        
        $('.message', $parent).text('Please wait...');
        $.post(ajaxurl, data, function(json) {
            $('.message', $parent).text(json.message);
            if (json.result == 'success') {
                $('input[type=text], textarea', $parent).val('').blur();
            }
        }, 'json');
        
        return false;
    });
    
});