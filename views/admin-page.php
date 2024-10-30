<div class="wrap">
<h1><?php esc_html_e( 'Instagram Feeds Settings', 'textdomain' );?></h1>


<?php


$instsLoginUrl = $instagram->getLoginUrl();

$OUTH = get_option( 'cp-insta-outhToken');

//var_dump($OUTH);

if(isset($_GET['code']) )
{
    if(get_option('cp-insta-outhToken')==''){
    $code = $_GET['code'];
    $outhToken = $instagram->getOAuthToken($code);
    //var_dump($outhToken);
    update_option( 'cp-insta-outhToken', $outhToken, true );
    }
}


$cp_insta_callback = get_option('cp-insta-callback');
$cp_insta_key = get_option('cp-insta-key');
$cp_insta_sec = get_option('cp-insta-sec');
$cp_insta_plugin_css = get_option('cp-insta-plugin-css');
?>



<form method="post" action="">
    <table class="form-table">
        <tr>
            <td><label>Instagram API Key:</label></td>
            <td><input type="text" class="regular-text" name="cp-insta-key" value="<?php echo $cp_insta_key;?>" /></td>
        </tr>
        <tr>
            <td><label>Instagram API Secret:</label></td>
            <td><input type="text" class="regular-text" name="cp-insta-sec" value="<?php echo $cp_insta_sec;?>" /></td>
        </tr>
        <tr>
            <td><label>Instagram API Callback:</label><br/>
            <small style="color:#FF99AA;">Copy the url from address bar</small>
            </td>
            <td><input type="text" class="regular-text" name="cp-insta-callback" value="<?php echo $cp_insta_callback;?>" /></td>
        </tr>
        <tr>
            <td colspan="2">
                <?php if($instsLoginUrl):?>
                <a href='<?php echo $instsLoginUrl;?>' class='cp-authenticate-btn'>Authenticate with Instagram</a>
                <?php endif;?>
            </td>
            
        </tr>
        <tr>
            <td><label>Instagram API Outh:</label></td>
            <td>
                Auth Token : <?php echo $outh->access_token;?><br/>
                UserName : <?php echo $outh->user->username;?>
            
            </td>
        </tr>
        
        <tr>
            <td><label>Display Likes Count:</label></td>
            <td><input type="checkbox" name="cp-insta-likes" value="1" <?php if(get_option('cp-insta-likes')==1){echo 'checked="checked"';}?>" /></td>
        </tr>
        <tr>
            <td><label>Display Comments Count:</label></td>
            <td><input type="checkbox" name="cp-insta-comment" value="1" <?php if(get_option('cp-insta-comment')==1){echo 'checked="checked"';}?>" /></td>
        </tr>
        <tr>
            <td><label>Use Plugin CSS:</label></td>
            <td><input type="checkbox" name="cp-insta-plugin-css" value="1" <?php if(get_option('cp-insta-plugin-css')==1){echo 'checked="checked"';}?>" /></td>
        </tr>
        <tr>
            <td colspan="2"><?php echo wp_nonce_field('cp-instagram-submission'); ?>
                <?php submit_button( 'Submit' );?>
            </td>
            
        </tr>
    </table>
   
    
</form>
</div>