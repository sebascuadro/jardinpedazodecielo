<?php /** Template version: 1.0.0 */ ?>

<h3><?php _e( 'Managed Groups', 'cuarmg' ); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th><label><?php 	
						if ( $is_own_profile ) : _e( 'You manage: ', 'cuarmg' );
						else : _e( 'This user manages: ', 'cuarmg' );
						endif;
				?></label></th>
			<td>
<?php		if ( empty( $groups_managed ) ) : ?>	
				<p><?php 	
							if ( $is_own_profile ) : _e( 'You do not manage any group', 'cuarmg' );
							else : _e( 'This user does not manage any group', 'cuarmg' );
							endif;
				?></p>
<?php 		else : ?>	
				<ul class="ul-disc">
<?php 			foreach ( $groups_managed as $group ) : ?>
					<li><?php echo get_the_title( $group ); ?></li>	
<?php 			endforeach; ?>
				</ul>
<?php 	endif; ?>
			</td>
		</tr>
			
			
		<tr>
			<th><label><?php 	
						if ( $is_own_profile ) : _e( 'You are a member of: ', 'cuarmg' );
						else : _e( 'This user is a member of: ', 'cuarmg' );
						endif;
				?></label></th>
			<td>
			<td>
<?php		if ( empty( $groups_subscribed ) ) : ?>	
				<p><?php 	
							if ( $is_own_profile ) : _e( 'You do not belong to any group', 'cuarmg' );
							else : _e( 'This user does not belong to any group', 'cuarmg' );
							endif;
				?></p>
<?php 		else : ?>	
				<ul class="ul-disc">
<?php 			foreach ( $groups_subscribed as $group ) : ?>
					<li><?php echo get_the_title( $group ); ?></li>	
<?php 			endforeach; ?>
				</ul>
<?php 	endif; ?>
			</td>
		</tr>	
	</tbody>
</table>