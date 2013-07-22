<div class="modal-form">

	<form action="<?php echo get_permalink($post_id); ?>" method="post">
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
		<input type="hidden" name="modal-form-sbumitted" value="y" />
		
		<table>
			<tr>
				<td><label for="tenten_uid">User Id</label></td>
				<td><input type="text" name="tenten[uid]" id="tenten_uid" ></td>
			</tr>
			<tr>
				<td><label for="tenten_pswd"> Password</label></td>
				<td><input type="password" name="tenten[pswd]" id="tenten_pswd" ></td>
			</tr>
		</table>
	
		<p> <input type="submit" value="Authorize" calss="button button-primary" /> </p>
	
	</form>

</div>