<?php
	global $tracker;
	
	if(!empty($_REQUEST['saveTracker'])) {
		$tracker->ht_add_meta('tracker_status', $_POST['switcher']);
	}

	if(!empty($_REQUEST['delete'])) {
		$tracker->ht_question_delete($_POST['ID']);
	}

	if(!empty($_REQUEST['update'])) {
		$tracker->ht_question_update($_POST['ID'], $_POST['status'], $_POST['type'], $_POST['multiple']);
	}

	if(!empty($_REQUEST['addQuestion'])) {
		$answers = array();
		if(!empty($_POST['ans1'])) {
			$answers[] = $_POST['ans1'];
		}
		if(!empty($_POST['ans2'])) {
			$answers[] = $_POST['ans2'];
		}
		if(!empty($_POST['ans3'])) {
			$answers[] = $_POST['ans3'];
		}
		if(!empty($_POST['ans4'])) {
			$answers[] = $_POST['ans4'];
		}
		if(!empty($_POST['ans5'])) {
			$answers[] = $_POST['ans5'];
		}
		if(!empty($_POST['ans6'])) {
			$answers[] = $_POST['ans6'];
		}
		if(!empty($_POST['ans7'])) {
			$answers[] = $_POST['ans7'];
		}
		if(!empty($_POST['ans8'])) {
			$answers[] = $_POST['ans8'];
		}
		if(!empty($_POST['ans9'])) {
			$answers[] = $_POST['ans9'];
		}
		if(!empty($_POST['ans10'])) {
			$answers[] = $_POST['ans10'];
		}

		$tracker->ht_question_add($_POST['question'], $answers, $_POST['type'], $_POST['multiple'], $_POST['status'], $_POST['color']);
	}

	$trackerStatus = $tracker->ht_get_meta_value('tracker_status');
	$questions = $tracker->ht_question_get_all();

?>

<div class="wrap">
	<?php echo "<h2>" . __('Health Tracker Settings', 'ht_lang') . "</h2>"; ?>
	<hr>

	<form action="" method="POST">
		<table class="form-table">
			<tbody>
				<tr>
					<td width="90%"><input type="checkbox" class="switcher" name="switcher" <?php if($trackerStatus) { echo 'checked'; } ?> value="<?php echo $trackerStatus; ?>"><?php echo __('Enable tracker', 'ht_lang'); ?></td>
					<td><input name="saveTracker" type="submit" class="button button-primary" value="<?php _e('Save Settings', 'ht_lang');?>" /></td>
				</tr>
			</tbody>
		</table>
	</form>

	<hr>
	<table width="100%">
		<tr>
			<td width="90%"><?php echo "<h2>" . __('Questions', 'ht_lang') . "</h2>"; ?></td>
			<td><button class="addQuestion button button-primary"><?php _e('Add new question', 'ht_lang'); ?></button></td>
		</tr>
	</table>

	<form action="" method="POST" class="addQ" style="display: none;">
		<table width="70%" style="text-align: left;">
			<tbody>
				<tr>
					<th scope="row" valign="middle">
						<label for="question"><?php _e('Question', 'ht_trans');?>:</label>
					</th>
					<td>
						<textarea cols="50" rows="6" name="question" required /></textarea>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans1"><?php _e('Answer 1', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans1" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans2"><?php _e('Answer 2', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans2" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans3"><?php _e('Answer 3', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans3" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans4"><?php _e('Answer 4', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans4" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans5"><?php _e('Answer 5', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans5" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans6"><?php _e('Answer 6', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans6" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans7"><?php _e('Answer 7', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans7" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans8"><?php _e('Answer 8', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans8" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans9"><?php _e('Answer 9', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans9" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="ans10"><?php _e('Answer 10', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="ans10" />
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="type"><?php _e('Type', 'ht_trans');?>:</label>
					</th>
					<td>
						<select name="type">
							<option value="1"><?php _e('Numeric', 'ht_lang'); ?></option>
							<option value="0"><?php _e('Text', 'ht_lang'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="multiple"><?php _e('Multiple answers', 'ht_trans');?>:</label>
					</th>
					<td>
						<select name="multiple">
							<option value="0"><?php _e('No', 'ht_lang'); ?></option>
							<option value="1"><?php _e('Yes', 'ht_lang'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="status"><?php _e('Status', 'ht_trans');?>:</label>
					</th>
					<td>
						<select name="status">
							<option value="1"><?php _e('Active', 'ht_lang'); ?></option>
							<option value="0"><?php _e('Inactive', 'ht_lang'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row" valign="middle">
						<label for="color"><?php _e('Color code', 'ht_trans');?>:</label>
					</th>
					<td>
						<input type="text" size="50%" name="color" value="" placeholder="color code - example: 737373" maxlength="6" minlength="6" required />
					</td>
				</tr>
				<tr>
					<td>
						<input name="addQuestion" type="submit" class="button button-primary" value="<?php _e('Add', 'ht_lang');?>" />
					</td>
				</tr>
			</tbody>
		</table>
	</form>
	<hr>

	<table width="100%" style="text-align: left;">
		<tbody>
			<th width="35%"><?php _e('Question', 'ht_lang'); ?></th>
			<th width="20%"><?php _e('Answers', 'ht_lang'); ?></th>
			<th width="5%"><?php _e('Answer Type', 'ht_lang'); ?></th>
			<th width="5%"><?php _e('Multiple', 'ht_lang'); ?></th>
			<th width="5%"><?php _e('Status', 'ht_lang'); ?></th>
			<th width="5%"><?php _e('Color', 'ht_lang'); ?></th>
			<th width="25%"><?php _e('Actions', 'ht_lang'); ?></th>
			<?php foreach ($questions as $question) { ?>
			<form action="" method="POST">
				<tr>
					<td><?php echo $question->question; ?></td>
					<td><?php 
					$answers = unserialize($question->answers); 

					foreach ($answers as $key => $answer) {
						echo 'Answer ' . intval($key+1) . ': ' . $answer . "<br>";
					}

					?></td>
					<td>
						<select name="type">
							<option <?php if($question->type) { echo 'selected'; } ?> value="1"><?php _e('Numeric', 'ht_lang'); ?></option>
							<option <?php if(!$question->type) { echo 'selected'; } ?> value="0"><?php _e('Text', 'ht_lang'); ?></option>
						</select>
					</td>
					<td>
						<select name="multiple">
							<option <?php if($question->multiple) { echo 'selected'; } ?> value="1"><?php _e('Multiple ON', 'ht_lang'); ?></option>
							<option <?php if(!$question->multiple) { echo 'selected'; } ?> value="0"><?php _e('Multiple OFF', 'ht_lang'); ?></option>
						</select>
					</td>
					<td>
						<select name="status">
							<option <?php if($question->status) { echo 'selected'; } ?> value="1"><?php _e('Active', 'ht_lang'); ?></option>
							<option <?php if(!$question->status) { echo 'selected'; } ?> value="0"><?php _e('Inactive', 'ht_lang'); ?></option>
						</select>
					</td>
					<td>
						<div style="width: 50px; display block; height: 20px; background: #<?php echo $question->color; ?>;"></div>
					</td>
					<td>
						<input type="hidden" name="ID" value="<?php echo $question->id; ?>">
						<input name="update" type="submit" class="button" value="<?php _e('Update', 'ht_lang');?>" />
						<input name="delete" type="submit" class="button" value="<?php _e('Delete', 'ht_lang');?>" />
					</td>
				</tr>
			</form>
			<?php } ?>
		</tbody>
	</table>

</div>

<script>
	jQuery(document).ready(function($) {
		$('.switcher').click(function() {
			if($(this).is(':checked')) {
				$(this).attr('value', 1);
			} else {
				$(this).attr('value', 0);
			}
		});

		$('.addQuestion').click(function() {
			if($('.addQ').is(':visible')) {
				$('.addQ').hide();
			} else {
				$('.addQ').show();
			}
		});
	});
</script>