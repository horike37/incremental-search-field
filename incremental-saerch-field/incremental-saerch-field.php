<?php

/*
 *	Advanced Custom Fields - Incremental search Field
 *
 */
 
if( !class_exists( 'Incremental_Search_Field' ) && class_exists( 'acf_Field' ) ) :
class Incremental_Search_Field extends acf_Field
{
	
	function __construct($parent)
	{

    	parent::__construct($parent);

    	$this->name = 'incremental_search';
		$this->title = __("Incremental Search Field",'acf');
		add_action('wp_ajax_autocmp', array(&$this, 'autocmp'));
		
   	}

	
	function create_options($key, $field)
	{
		$field['acf-search'] = isset($field['acf-search']) ? $field['acf-search'] : '';
		$choices = get_post_types( array('public' => true) );

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Incremental Search",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'select',
					'name'	=>	'fields['.$key.'][acf-search]',
					'value'	=>	$field['acf-search'],
					'choices' => $choices,
				));
				?>
			</td>
		</tr>
		<?php
	}

	function create_field($field)
	{
		$field['acf-search'] = isset($field['acf-search']) ? $field['acf-search'] : '';
		
		echo '<p><input type="text" value="' . $field['value'] . '" id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" /></p>';
?>
<script type="text/javascript" >
		acfAutoComplete( '.incremental_search', '<?php echo admin_url("admin-ajax.php?action=autocmp"); ?>', 0, '<?php echo $field["acf-search"] ?>' );
</script>
<?php
	}

	function admin_head()
	{
		echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/incremental-saerch-field/resource/jquery.autocomplete.css" />';
		echo '<script type="text/javascript" src="'.get_template_directory_uri().'/incremental-saerch-field/resource/jquery.autocomplete.min.js" ></script>';
?>

<script type="text/javascript" >
function acf_formatItem(row) {
	return row[1];
}

function acf_formatResult(row) {
	return row[1].replace(/(<.+?>)/gi, '');
}

function acfAutoComplete( p_target, p_url, p_min_chars, post_type ) {

	jQuery( ""+p_target ).autocomplete( p_url, {
		selectFirst: true,
		formatItem: acf_formatItem,
		formatResult: acf_formatResult,
		minChars: p_min_chars,
		multipleSeparator: "",
		extraParams: {'post_type':post_type}
	});
}
</script>	
<?php
	}
	
	function update_value($post_id, $field, $value)
	{
		parent::update_value($post_id, $field, $value);
	}
	
	function get_value($post_id, $field)
	{
		$value = parent::get_value($post_id, $field);
		
		return $value;		
	}
	
	function get_value_for_api($post_id, $field)
	{
		$value = $this->get_value($post_id, $field);

		return $value;

	}

	function autocmp() {
		$q = strtolower($_GET["q"]);
		if (!$q) return;

		$post_type = strtolower($_GET["post_type"]);
		if (!$q) return;

		$result = get_posts( array( 'post_type' => $post_type, 'numberposts' => -1, 'post_status' => 'publish' ) );

		if ($result) {
			foreach ( $result as $r ) {
				if (strpos(strtolower($r->post_title), $q) !== false) {
					echo "$r->post_name|$r->post_title\n";
				}
			}
		}
		exit;
	}
	
}
endif;

?>