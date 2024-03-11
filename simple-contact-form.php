<?php
/**
* plugin name: simple contact form
* Description: simple contact form
* Author: Mr sani
* Author URI: https://sania.com
* version: 1.0.0
* Text Domain: simple contact form
*/
if( !defined('ABSPATH')){
    echo "what are trying you to do";
    exit;
}
class SimpleContactForm{
    public function __construct()
    {
        //create a custom post type
add_action('init', array($this, 'create_custom_post_type'));


//create an assets css ,js,etc
add_action('wp_enqueue_scripts', array($this, 'load_assets'));


// add shortcode
add_shortcode('ccontact-form', array($this, 'load_shortcode'));
//load javascript
add_action('wp_footer', array($this, 'load_scripts'));
//Register REST API
add_action('rest_api_init', array($this, 'register_rest_api'));

    }
    public function create_custom_post_type()
{
    $args = array(
        'public' => true,
        'has_archieve' => true,
        'supports' => array('title'),
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'capability' => 'manage_options',
        'labels' => array(
            'name' => 'Contact Form',
            'singular_name' => 'Contact Form Entry',
        ),
        'menu_icon' => 'dashicons-media-text',
    );
    register_post_type('simple_contact_form', $args);
}

public function load_assets()
{
 wp_enqueue_style(
        'simple-contact-form',
        plugin_dir_url(__FILE__) . 'css/simple-contact-form.css',
        array(),
        1,
        'all'
    );
    wp_enqueue_script(
        'simple-contact-form',
        plugin_dir_url(__FILE__) . 'js/simple-contact-form.js',
        array('jquery'),
        1,
        true

    );
}
public function load_shortcode()
{?>
<div class="simple-contact-form">

<h1>Send Us an Email</h1>

<p>Please fill the below form</p>
<form id="simple=contact-form__form">
    <div class="form-group mb-2">
<input name="name" type="text" placeholder="Name" class="form-control">
</div>
<div class="form-group mb-2">
<input  name="email" type="email" placeholder="Email" class="form-control">
</div>
<div class="form-group mb-2">
<input name="phone" type="tel" placeholder="phone" class="form-control">
</div>
<div class="form-group mb-2">
<textarea  name="message" placeholder="type your message" class="form-control"></textarea>
</div>
<div class="form-group">
<button class="btn btn-success btn-block w-100">Send Message</button>
</div>
</form>
</div>
<?php
}
public function load_scripts()
{?>

<script>
    var nonce = '<?php echo wp_create_nonce('wp_rest');?>';
    (function($){
 $('#simple-contact-form__form').submit(function(event){
        event.preventDefault();

    var form = $(this).serialize();

    console.log(form);
    $.ajax({
        method:'post',
        url: '<?php echo get_rest_url(null, 'simple-contact-form/send-email');?>'
        headers: { 'X-WP-Nonce': nonce },
        data: form
    })

    });

})(jQuery)
</script>




<?php
}
public function register_rest_api()
{
register_rest_route( 'simple-conatct-form','send-email', array(
    'methods' => 'POST',
    'callback' => array($this, 'handle_contact_form')

));
}
public function handle_contact_form($data)
{
$headers = $data->get_headers();
$params = $fata->get_params();
echo json_encode($headers);
$nonce = $headers['x_wp_nonce'][0];
if(wp_verify_noce($nonce,'wp_rest'))
{
    return new WP_REST_Responce('message not sent', 422);
}
$post_id = wp_insert_post([
    'post_stype' => 'simple_contact_form',
    'post_title' => 'contact_enquiry',
    'post_status' => 'publish',
]);
if ($post_id)
{
    return new WP_REST_Responce('thank u for ur email', 200);

}
}





    
}


new SimpleContactForm;
?>