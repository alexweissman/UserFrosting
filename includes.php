<!-- Common header includes for all display pages -->

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="<?php echo $css_js_url; ?>css/favicon.ico" />

<!-- Core CSS -->
<link rel="stylesheet" href="<?php echo $css_js_url; ?>css/bootstrap.css">

<?php
    global $master_account;
    if(isset($_SESSION["userCakeUser"]) && is_object($_SESSION["userCakeUser"]) and $_SESSION["userCakeUser"]->user_id == $master_account){
        echo '<link rel="stylesheet" href="'.$css_js_url.'css/sb-admin-master.css">';
    } else {
        echo '<link rel="stylesheet" href="'.$css_js_url.'css/sb-admin.css">';
    }
?>

<link rel="stylesheet" href="<?php echo $css_js_url; ?>css/font-awesome.min.css">

<!-- Core JavaScript -->
<script src="<?php echo $css_js_url; ?>js/jquery-1.10.2.min.js"></script>
<script src="<?php echo $css_js_url; ?>js/bootstrap.js"></script>
<script src="<?php echo $css_js_url; ?>js/userfrosting.js"></script>

<!-- Adjust padding for fixed navbars -->
<script>
    $(document).ready(function(){
        $(document.body).css('padding-top', $('.navbar-fixed-top').height() + 10);
        $(window).resize(function(){
            $(document.body).css('padding-top', $('.navbar-fixed-top').height() + 10);
        });
    });
</script>
