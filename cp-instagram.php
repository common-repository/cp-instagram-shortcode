<?php
/*
Plugin Name: CP Instagram Shortcode
Plugin URI: http://chandan.byethost11.com/my-wordpress-plugins/
Description: Instagram Images Feeds Shortcode
Version: 10.0.1
Author: Chandan Pradhan
Author URI: http://www.chandan.byethost11.com/
*/

if(!defined('ABSPATH')) exit();


require_once('lib/Instagram.php');
require_once('lib/InstagramException.php');
use MetzWeb\Instagram\Instagram;


class CPInstagram{

function __construct()
{
    add_action( 'admin_menu',array($this, 'insta_register_menu_page') );
    add_action('admin_init',array($this,'reg_insta_settings'));
    add_shortcode('cp-instagram',array('CPInstagram','InstagramImages'));
    
    if(get_option('cp-insta-plugin-css'))
        add_action('init',array($this,'add_cp_insta_css'));
}


function add_cp_insta_css(){
    wp_enqueue_style('cp-instagram-style',plugins_url( 'css/cp-instagram-style.css', __FILE__ ));
    add_thickbox();
}

function reg_insta_settings(){
    register_setting( 'cp-insta-settings', 'cp-insta-settings' );
}



function insta_register_menu_page(){
    add_menu_page( 
        __( 'Instagram', 'insta' ),
        'instagram',
        'manage_options',
        'instamenupage',
        array($this,'insta_menu_page'),
        'dashicons-format-gallery',
        6
    );
}

 
/**
 * Display a custom menu page
 */
function insta_menu_page(){
    
    
    
    if(isset($_POST))
    {
        $key = isset($_POST['cp-insta-key'])?$_POST['cp-insta-key']:'';
        $secret = isset($_POST['cp-insta-sec'])?$_POST['cp-insta-sec']:'';
        $callback = isset($_POST['cp-insta-callback'])?$_POST['cp-insta-callback']:'';
        $accessToken = isset($_POST['cp-insta-outhToken'])?$_POST['cp-insta-outhToken']:'';
        $likesCount = isset($_POST['cp-insta-likes'])?$_POST['cp-insta-likes']:0;
        $commentsCount = isset($_POST['cp-insta-comment'])?$_POST['cp-insta-comment']:0;
        $pluginCSS = isset($_POST['cp-insta-plugin-css'])?$_POST['cp-insta-plugin-css']:0;
        $nonce = $_REQUEST['_wpnonce'];
        if(wp_verify_nonce($nonce,'cp-instagram-submission')){
            update_option( 'cp-insta-key', sanitize_text_field($key), true );
            update_option( 'cp-insta-sec', sanitize_text_field($secret), true );
            update_option( 'cp-insta-callback', sanitize_text_field($callback), true );
           // update_option( 'cp-insta-outhToken', sanitize_text_field($accessToken), true );
            update_option( 'cp-insta-likes', sanitize_text_field($likesCount), true );
            update_option( 'cp-insta-comment', sanitize_text_field($commentsCount), true );
            update_option( 'cp-insta-plugin-css', sanitize_text_field($pluginCSS), true );
        }
    }
    
        
    

    $instagram = new Instagram(array(
        'apiKey'      => get_option('cp-insta-key'),
        'apiSecret'   => get_option('cp-insta-sec'),
        'apiCallback' => get_option('cp-insta-callback')
    ));
    
    
    $outh = get_option('cp-insta-outhToken');
    
    
    
    require_once(__DIR__.'/views/admin-page.php');
}





function InstagramImages()
{

    //require_once('lib/Instagram.php');
    //require_once('lib/InstagramException.php');    
    //use MetzWeb\Instagram\Instagram;

    $instagram = new Instagram(array(
        'apiKey'      => get_option('cp-insta-key'),
        'apiSecret'   => get_option('cp-insta-sec'),
        'apiCallback' => get_option('cp-insta-callback')
    ));

    try{
        
    $commentsCount = get_option('cp-insta-comment',true);
    $likesCount = get_option('cp-insta-likes',true);
    $outh = get_option('cp-insta-outhToken',true);
    $instagram->setAccessToken($outh);

// get all user likes
$medias = $instagram->getUserMedia();

//echo "<pre>";print_r($medias);echo "</pre>";
if($medias->meta->code ==200){
if($display=='json'){
$output = array();
foreach($medias->data as $media)
{
    
    $d['Likes'] = $media->likes->count;
    $d['Comments'] = $media->comments->count;
    $d['standard_resolution'] = $media->images->standard_resolution->url;
    $d['thumbnail'] = $media->images->thumbnail->url;
    
    array_push($output,$d);
}

return json_encode($output);
}else{
    $output = '<ul class="cp-instagram-imgs">';
    foreach($medias->data as $media)
    {
        $output.='<li class="img-item">';
        $output.='<a class="thickbox" href="'.$media->images->standard_resolution->url.'" title="Comments: '.$media->comments->count.' | Likes: '.$media->likes->count.'">';
        $output.='<img src="'.$media->images->standard_resolution->url.'" alt="aa" class="cp-insta-img"/>';
        
        if($commentsCount==1){
            $output.='<span class="ccount" title="Comments">'.$media->comments->count.'</span>';
        }
        if($likesCount==1){
            $output.='<span class="lcount" title="Likes">'.$media->likes->count.'</span>';
        }
        
        
        $output.='</a></li>';
        //$d['Likes'] = $media->likes->count;
        //$d['Comments'] = $media->comments->count;
        //$d['standard_resolution'] = $media->images->standard_resolution->url;
        //$d['thumbnail'] = $media->images->thumbnail->url;
        //
        //array_push($output,$d);
    }
    $output .= '</ul>';
    return $output;
}
}else{
    return $medias->meta->error_message;
}

    }catch(Exception $e)
    {
           return json_encode(array("Error" => $e));
    }
}



}
new CPInstagram();