<?php
	global $tracker;
	
	// Set correct timezone

	date_default_timezone_set('europe/bucharest');

	$routines = $tracker->ht_routine_get_all();

	$questions = $tracker->ht_question_get_all(true);

	$have_q = 1;
	if(!$questions) {
		$have_q = 0;
	}


	// Only for profile's owner

	if(bp_is_home()) {
		
		if(!empty($_REQUEST['sendAnswers'])) {

			$uniqueIndex = uniqid() . (int)$_POST['routineId'];
			foreach ($_POST as $key => $value) {

				if(strpos($key,'answer')) {
					$newKey = explode("_", $key);
					$key = $newKey[0];
				}

				if(is_numeric($key)) {
					$tracker->ht_question_answer($uniqueIndex, $key, (int)$_POST['routineId'], $value);
				}
			}
		}

		if(!$routines) {
			echo "<div id=\"message\" class=\"info\">
					<p>You haven't set any variable, so you cannot make an entry. Please set your variables first.</p>
				</div>";
		} else {
	?>

		<div class="hr-title hr-full hr-double"><abbr><?php _e('Create new entry', 'ht_lang');?></abbr></div>

		<label for="routine"><?php _e('Pick a variable', 'ht_trans'); ?>:</label><br>
		<select class="routineSelect" name="routine" width="300px">
			<?php foreach ($routines as $routine) {
				echo "<option value=$routine->id>$routine->name</option>";
			} ?>
		</select>
		<input name="createEntry" class="createEntry" type="submit" class="button button-primary" value="<?php _e('Create New Entry', 'ht_lang');?>" />

		<?php
			if(!$have_q) {
				echo "<div id=\"message\" class=\"info\">
						<p>There are no question in the system. Please try again later.</p>
					</div>";
			} else {
		?>
		
		<form method="POST" action="" class="qForm" style="display: none;">
			<hr>
			<dl class="dl-horizontal field_1 field_name required-field visibility-public field_type_textbox">
				<?php
					foreach ($questions as $question) {
						echo "<dd><b>$question->question</b></dd>";
						
						$answers = unserialize($question->answers);
						
						if($question->multiple) {
							echo "<dd>";

							foreach ($answers as $answer) {
								$unq = uniqid();
								echo "<input type=\"checkbox\" name=\"" . $question->id . "_answer_" . $unq . "\" value=\"$answer\"> <span style=\"padding: 0 10px 0 0;\">$answer</span>";
							}
							echo "</dd>";
						} else {
							echo "<dd>";
							foreach ($answers as $answer) {
								echo "<input type=\"radio\" name=\"$question->id\" required value=\"$answer\"> <span style=\"padding: 0 10px 0 0;\">$answer</span>";
							}
							echo "</dd>";
						}		
						
					}
				?>
				
			</dl>
			<br />
			<input type="hidden" class="routineId" value="" name="routineId">
			<input name="sendAnswers" type="submit" class="button button-primary" value="<?php _e('Send', 'ht_lang');?>" />
			<input name="close" type="button" class="close button button-primary" value="<?php _e('Abandon', 'ht_lang');?>" />
		</form>

<?php
		}
	} 
} 
?>

		<?php

		// Graphic data
		if(!isset($_REQUEST['period']) || empty($_REQUEST)) {
			$now = date('Y-m-d H:i:s');
			$start = date('Y-m-d H:i:s', strtotime("-7 day"));

			$q_numeric = $tracker->ht_get_answers(null, 1, $start, $now);
			$q_text = $tracker->ht_get_answers(null, 0, $start, $now);

			$_REQUEST['period'] = "week";
		}

		if(!empty($_REQUEST['routine']) && !empty($_REQUEST['period'])) {
			$now = date('Y-m-d H:i:s');

			switch ($_REQUEST['period']) {
				case 'week':
					$start = date('Y-m-d H:i:s', strtotime("-7 day"));
					break;
				case 'month':
					$start = date('Y-m-d H:i:s', strtotime("-30 day"));
					break;
				case '3month':
					$start = date('Y-m-d H:i:s', strtotime("-90 day"));
					break;
				case '6month':
					$start = date('Y-m-d H:i:s', strtotime("-180 day"));
					break;
				case 'year':
					$start = date('Y-m-d H:i:s', strtotime("-365 day"));
					break;
				default:
					$start = date('Y-m-d H:i:s');
			}

			$q_numeric = $tracker->ht_get_answers((int) $_POST['routine'], 1, $start, $now);
			$q_text = $tracker->ht_get_answers((int) $_POST['routine'], 0, $start, $now);
		}
		
		$num_q = array();
		$text_q = array();

		if(!$q_numeric) {
			$n_msg = 1;
		} else {
			$n_msg = 0;
		}

		if(!$q_text) {
			$t_msg = 1;
		} else {
			$t_msg = 0;
		}

		foreach ($q_numeric as $data) {
			if(!array_key_exists($data->q_id, $num_q)) {
				$num_q[$data->q_id][$data->answer] = $data->created;
			} else {
				$num_q[$data->q_id][$data->answer] = $data->created;;
			}
		}

		foreach ($q_text as $data) {
			if(!array_key_exists($data->q_id, $num_q)) {
				$text_q[$data->q_id][] = $data->answer;
			} else {
				$text_q[$data->q_id][] = $data->answer;
			}
		}

		$lineObj = array();

		$interval = array();

		foreach ($num_q as $question => $answers) {
			$answerArray = array();

			foreach($answers as $key => $value) {
				$interval[] = $value;
				$answerArray[] = $key;
			}

			$question = $tracker->ht_question_get_one($question);

			$lineObj[] = array(
				'label' 				=> $question->question,
				'fillColor'				=> $tracker->hex2rgba($question->color, 0.2),
				'strokeColor'			=> $tracker->hex2rgba($question->color, 1),
				'pointColor'			=> $tracker->hex2rgba($question->color, 1),
				'pointStrokeColor'		=> "#fff",
				'pointHighlightFill'	=> "#fff",
				'pointHighlightStroke'	=> $tracker->hex2rgba($question->color, 1),
				'data'					=> $answerArray
				);
		}

		$pieObj = array();

		foreach ($text_q as $question => $answers) {

			$question = $tracker->ht_question_get_one($question);

			$checker = array();
			foreach ($answers as $key => $value) {
				if(!array_key_exists($value, $checker)) {

					$checker[$value] = uniqid();

					$pieObj[$checker[$value]] = array(
					'value'			=> 1,
	                'color'			=> $tracker->hex2rgba($question->color, 1),
	                'label'			=> "Q #" . $question->id . " - A: " . $value,
	                'labelColor'	=> 'white',
	                'labelFontSize'	=> '16'
					);
				} else {
					$pieObj[$checker[$value]]['value'] += 1;
				}
				
			}
		}

		if($routines) { ?>

		<div class="hr-title hr-full hr-double"><abbr><?php _e('Generate', 'ht_lang');?></abbr></div>
		<form method="post" action="" class="generator">
			<?php _e('Pick a variable', 'ht_trans'); ?>:
			<select class="routineSelect" name="routine" width="300px" required>
				<?php foreach ($routines as $routine) {
					if(!empty($_REQUEST['routine']) && $routine->id == $_REQUEST['routine']) {
						echo "<option value='$routine->id' selected>$routine->name</option>";
					} else {
						echo "<option value='$routine->id'>$routine->name</option>";
					}
					
				} ?>
			</select>
			<br />
			<?php _e('Graph display', 'ht_trans'); ?>:
			<div class="radios">
				<input type="radio" id="one" name="period" value="week" <?php if(!empty($_REQUEST['period']) && $_REQUEST['period'] == 'week') { echo 'checked'; } ?>>
				<label for="one">1 week</label>
				<input type="radio" id="two" name="period" value="month" <?php if(!empty($_REQUEST['period']) && $_REQUEST['period'] == 'month') { echo 'checked'; } ?>>
				<label for="two">1 month</label>
				<input type="radio" id="three" name="period" value="3month" <?php if(!empty($_REQUEST['period']) && $_REQUEST['period'] == '3month') { echo 'checked'; } ?>>
				<label for="three">3 months</label>
				<input type="radio" id="four" name="period" value="6month" <?php if(!empty($_REQUEST['period']) && $_REQUEST['period'] == '6month') { echo 'checked'; } ?>>
				<label for="four">6 months</label>
				<input type="radio" id="five" name="period" value="year" <?php if(!empty($_REQUEST['period']) && $_REQUEST['period'] == 'year') { echo 'checked'; } ?>>
				<label for="five">1 year</label>
				<input name="getData" type="submit" style="display: none;" class="button generate button-primary" value="<?php _e('Generate', 'ht_lang');?>" />
			</div>
		</form>

		<div class="graph-numeric-data" <?php if(!$q_numeric) { echo 'style="display: none;"'; } ?>>
			<div class="hr-title hr-full hr-double"><abbr><?php _e('Numeric graph', 'ht_lang');?></abbr></div>
			<canvas id="line-area" width="400" height="200"></canvas>
			<div id="legend-N"></div>
			<br><br>
		</div>
		<div class="graph-text-data" <?php if(!$q_text) { echo 'style="display: none;"'; } ?>>
			<div class="hr-title hr-full hr-double"><abbr><?php _e('Text graph', 'ht_lang');?></abbr></div>
			<canvas id="pie-area" width="500" height="500"></canvas>
			<div id="legend-T"></div>
		</div>

		<?php } ?>

		<?php 
			$answers = $tracker->ht_get_all_answers();
			
			$data = array();
			foreach ($answers as $obj) {
				$data[$obj->unq][] = array(
					'routine'	=> $obj->name,
					'question'	=> $obj->question,
					'answer'	=> $obj->answer,
					'created'	=> $obj->created
					);
			}

		?>

		<div class="hr-title hr-full hr-double"><abbr><?php _e('History', 'ht_lang');?></abbr></div>
		<div class="history-list">
			<table width="100%">
				<tr>
					<th width="25%">Variable</th>
					<th width="20%">Answered at</th>
					<th width="55%"></th>
				</tr>
				<?php 

					foreach ($data as $item) {
					
						$content = '';

						foreach ($item as $value) {
							$content .= "<tr>
								<td>" . $value["question"] . "</td>
								<td>" . $value["answer"] . "</td>
							</tr>";
						}


						echo "<tr>
							<td>" . $item[0]["routine"] . "</td>
							<td>" . $item[0]["created"] . "</td>
							<td>
								<input type=\"button\" class=\"details\" value=\"show answers\">
								<div style=\"display: none;\">
									<table>$content</table>
								</div>
							</td>
						</tr>";
					}
				?>
			</table>
		</div>
		

<script>

	jQuery(document).ready(function($) {
		var display = <?php echo $have_q; ?>;

		if(display == 0) {
			$('.createEntry').prop('disabled', true);
		}	

		$('.createEntry').click(function() {
			if($('.qForm').is(':visible')) {
				$('.qForm').fadeOut();
				$('.routineSelect').prop('disabled', false);
				$(this).attr('value', 'Create New Entry');
			} else {
				$('.qForm').fadeIn();
				$('.routineSelect').prop('disabled', true);
				$('.routineId').attr('value', $('.routineSelect').val());
				$(this).attr('value', 'Close');
			}
		});

		$('.close').click(function() {
			if($('.qForm').is(':visible')) {
				$('.qForm').fadeOut();
				$('.routineSelect').prop('disabled', false);
			}
		});

		$('.details').click(function() {
			if($(this).nextAll("div").first().is(":visible")) {
				$(this).nextAll("div").first().fadeOut();
				$(this).attr("value", "show answers");
			} else {
				$(this).nextAll("div").first().fadeIn();
				$(this).attr("value", "hide answers");
			}
			
		});

		$('.generate').click(function() {
			if($('.startD').val() > $('.endD').val()) {
				$('#message').fadeIn();
				return false;
			} else {
				$('#message').fadeOut();
			}
		});

		$('.radios input[type=radio]').on('change', function() {
			$(this).closest("form").submit();
		});

		$('.routineSelect').on('change', function() {
			$(this).closest("form").submit();
		});

		var randomScalingFactor = function(){ return Math.round(Math.random()*100)};
		var lineChartData = {
			labels : <?php echo json_encode($interval); ?>,
			datasets : <?php echo json_encode($lineObj); ?>

		}

		var pieData = <?php echo json_encode($pieObj); ?>;

		window.onload = function() {
			var ctx = document.getElementById("line-area").getContext("2d");
			window.myLine = new Chart(ctx).Line(lineChartData, {
				responsive: true,
				legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li style=\"color:<%=datasets[i].strokeColor%> !important; font-weight: bold;\"><span></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
			});

			var ctx = document.getElementById("pie-area").getContext("2d");
			window.myPie = new Chart(ctx).Pie(pieData, {
				legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li style=\"color:<%=segments[i].fillColor%>; font-weight: bold;\"><span></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
			});

			var legendN = myLine.generateLegend();
			$('#legend-N').append(legendN);

			var legendT = myPie.generateLegend();
			$('#legend-T').append(legendT);
		}

	});

</script>