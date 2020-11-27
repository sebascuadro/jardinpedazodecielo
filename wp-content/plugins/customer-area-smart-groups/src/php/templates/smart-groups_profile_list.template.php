<?php /** Template version: 1.0.0 */ ?>

<h3><?php _e( 'Smart Groups', 'cuarsg' ); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th><label><?php _e( 'Member of ', 'cuarsg' ); ?></label></th>
			<td>
<?php		if ( empty( $groups ) ) : ?>
				<p><?php 	
							if ( $is_own_profile ) : _e( 'You do not belong to any group', 'cuarsg' );
							else : _e( 'This user does not belong to any group', 'cuarsg' );
							endif;
				?></p>
<?php 		else : ?>	
				<ul class="ul-disc">
<?php 			foreach ( $groups as $group ) : ?>
					<li><?php echo get_the_title( $group ); ?></li>	
<?php 			endforeach; ?>
				</ul>
<?php 	endif; ?>
			</td>
		</tr>	
	</tbody>
</table>