<?php /** Template version: 1.0.1

 -= 1.0.1 =-
 * Replace __ by _e where needed when no groups

 */ ?>

<h3><?php _e( 'Managed Groups', 'cuarmg' ); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th><label for="cuar_managed_group_ids"><?php _e( 'Edit groups managed', 'cuarmg' ); ?></label></th>
			<td>			
<?php	if ( empty( $all_groups ) ) : ?>
				<p><?php _e('You have not yet created any managed groups.', 'cuarmg'); ?></p>
<?php 	else : ?>
			
				<select id="cuar_managed_group_ids" class="groups" name="cuar_managed_group_ids[]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Choose groups', 'cuarmg' ); ?>">
	
<?php 		foreach ( $all_groups as $group ) :
				$is_member = in_array( $group, $groups_managed );
				
				$selected = $is_member ? ' selected="selected" ' : '';
?>
					<option value="<?php echo $group->ID; ?>" <?php echo $selected; ?>><?php echo get_the_title( $group ); ?></option>
<?php 		endforeach; ?>
				</select>
				
				<script type="text/javascript">
					<!--
					jQuery("document").ready(function($){
						$("#cuar_managed_group_ids").select2({
							<?php if(!is_admin()) echo "dropdownParent: $('#cuar_managed_group_ids').parent(),"; ?>
							width:						"100%"
						});
					});
					//-->
				</script>
<?php 	endif; ?>
			</td>
		</tr>
			
			
		<tr>
			<th><label for="cuar_subscribed_group_ids"><?php _e( 'Edit groups subscribed', 'cuarmg' ); ?></label></th>
			<td>		
<?php	if ( empty( $all_groups ) ) : ?>
				<p><?php _e('You have not yet created any managed groups.', 'cuarmg'); ?></p>
<?php 	else : ?>
			
				<select id="cuar_subscribed_group_ids" class="groups" name="cuar_subscribed_group_ids[]" multiple="multiple" data-placeholder="<?php esc_attr_e( 'Choose groups', 'cuarmg' ); ?>">
	
<?php 		foreach ( $all_groups as $group ) :
				$is_member = in_array( $group, $groups_subscribed );
				
				$selected = $is_member ? ' selected="selected" ' : '';
?>
					<option value="<?php echo $group->ID; ?>" <?php echo $selected; ?>><?php echo get_the_title( $group ); ?></option>
<?php 		endforeach; ?>
				</select>
				
				<script type="text/javascript">
					<!--
					jQuery("document").ready(function($){
						$("#cuar_subscribed_group_ids").select2({
							<?php if(!is_admin()) echo "dropdownParent: $('#cuar_subscribed_group_ids').parent(),"; ?>
							width:						"100%"
						});
					});
					//-->
				</script>
<?php 	endif; ?>
			</td>
		</tr>	
	</tbody>
</table>