<?php
/***
 ** Plugin Name: Sortable Tags
 ** Author: Ehab Alsharif
 ** Description: Make your tags sortable 
 ** Version: 1.0
 ** License: GNU GPL v2 or later
 ***/
 

/**
 * Modifiy the original post_tag taxonomy
 *   
 * A callback function to the init action 
 *
 * @version 1.0
 * @return  void 
 ***/
function st_modify_taxonomy() {
    // get the arguments of the already-registered taxonomy
    $args = get_taxonomy( 'post_tag' );
    $args->sort = true;

    register_taxonomy( 'post_tag', 'post', (array) $args );
}
// hook it 11 to override the original register_taxonomy function
add_action( 'init', 'st_modify_taxonomy', 11 );


/**
 * Modifiy the $args array that will be based to the wp_get_object_terms_args function 
 
 *   
 * A callback function to the st_filter_get_the_terms_args filter 
 *
 * @param array $args 
 * @version 1.0
 * @return  array $args 
 ***/
function st_filter_get_the_terms_args($args){
	$args['orderby'] = 'term_order';
	return $args;
}
add_filter('wp_get_object_terms_args', 'st_filter_get_the_terms_args', 1, 1);

/**
   * Inject Js in post pages
   * 
   * A callback function to the admin_enqueue_scripts event 
   * 
   * @version 1.0
   * @param   string $hook hook name
   * @return  void
***/
function st_inject_js($hook){

	// if page is not post then do not inject
	if ($hook != 'post.php' && $hook != 'post-new.php')
		return;

	echo '
		<script>
			document.addEventListener("DOMContentLoaded", function(){ 
				jQuery(".tagchecklist").sortable({
					stop: function(e){
						var htmlCollection = e.target.children;
						var tagsTextArea = document.getElementById("tax-input-post_tag");
						tagsTextArea.value = "";
						for (var i = 0, l = htmlCollection.length; i < l; i++ ){
							var button = htmlCollection[i].children[0];
							var text = button.innerText.replace("Remove term:", "").trim();
							if (i != l -1)
								tagsTextArea.value = tagsTextArea.value + text + ",";
							else 
								tagsTextArea.value = tagsTextArea.value + text;
						}
						
					}
				});
			});
		</script>
	';

}
add_action('admin_enqueue_scripts', 'st_inject_js', 10);





