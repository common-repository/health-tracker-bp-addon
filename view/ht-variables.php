<?php
	global $tracker;

	if(!empty($_REQUEST['removeRoutine'])) {
		$tracker->ht_routine_delete($_POST['routineId']);
	}

	if(!empty($_REQUEST['addRoutine'])) {
		$tracker->ht_routine_add($_POST['routineName']);
	}

	if(!empty($_REQUEST['routineNewName']) && !empty($_REQUEST['routineId'])) {
		$tracker->ht_routine_edit($_POST['routineId'], $_POST['routineNewName']);
	}

	$routines = $tracker->ht_routine_get_all();
?>

<div class="hr-title hr-full hr-double"><abbr><?php _e('View/Add variables', 'ht_lang');?></abbr></div>

<form action="" method="POST">
	<input type="text" name="routineName" class="settings-input small" placeholder="ex: Diabetes" />
	<input name="addRoutine" type="submit" class="button button-primary" value="<?php _e('Add Variable', 'ht_lang');?>" />
</form>

<br />

<?php
	if(!$routines) {
		echo "<div id=\"message\" class=\"info\">
				<p>You haven't set any variable. Feel free to add some.</p>
			</div>";
	}
?>

<table width="100%">
	<th width="50%"><?php _e('Variable Name', 'ht_lang');?></th>
	<th width="50%"><?php _e('Actions', 'ht_lang');?></th>
	<?php foreach($routines as $key => $routine) { ?>
	<tr>
		<td><input type="text" class="nameEdit none" value="<?php echo $routine->name; ?>" disabled/></td>
		<td>
			<form action="" method="POST">
				<input name="routineId" type="hidden" value="<?php echo $routine->id; ?>" />
				<input name="routineNewName" type="hidden" class="newName" value="" />
				<input type="button" class="editRoutine button button-primary" value="<?php _e('Edit', 'ht_lang');?>" />
				<input type="button" class="cancelEdit button button-primary hide" value="<?php _e('Cancel', 'ht_lang');?>" />
				<input name="removeRoutine" type="submit" class="button button-primary" value="<?php _e('Remove', 'ht_lang');?>" />
			</form>
		</td>
	</tr>
	<?php } ?>
</table>

<script type="text/javascript">
	
	jQuery(document).ready(function($) {

		$('.editRoutine').click(function(){
			if($(this).attr('value') == 'Save') {
				$(this).prev('input').attr('value', $(this).closest('td').prev('td').find('.nameEdit').attr('value'));
				$(this).closest("form").submit();
			} else {
				$(this).attr('value', 'Save');
				$(this).addClass('btn-action');
				$(this).closest('td').prev('td').find('.nameEdit').prop('disabled', false);
				$(this).closest('td').prev('td').find('.nameEdit').removeClass('none');
				$(this).next('input').removeClass('hide');
			}
		});

		$('.cancelEdit').click(function(){
			$(this).prev('input').attr('value', 'Edit');
			$(this).prev('input').removeClass('btn-action');
			$(this).closest('td').prev('td').find('.nameEdit').prop('disabled', true);
			$(this).closest('td').prev('td').find('.nameEdit').addClass('none');
			$(this).addClass('hide');
		});

	});

</script>