<?php

$levels = [
	0,
	200,
	500,
	1000,
	2000,
	4000,
	7500,
	12500,
	25000,
	50000,
];

function expchat_level($points){
	for($i = 0; $i < count($levels); $i++){
		if($points > $levels[$i]){ continue; }
		return $i;
	}
}

if($telegram->is_chat_group()){
	if($telegram->words() == mt_rand(2, 9)){
		$timeout = $pokemon->settings($telegram->user->id, 'expchat_timeout');
		if(empty($timeout) or time() >= $timeout){
			$points = $pokemon->settings($telegram->user->id, 'expchat_points');

			$curlev = expchat_level($points);
			$newpoints = $points + $telegram->words();

			$nextlev = expchat_level($newpoints);

			if($nextlev > $curlev){
				$telegram->send
					->notification(FALSE)
					->text($telegram->user->first_name ." ha subido al *nivel " .$nextlev ."*!", TRUE)
				->send();
			}
			// La recompensa será el número de palabras que haya tocado, para hacer el factor diferencial.
			// Puede favorecer a los spamers, así que cuidado.
			$pokemon->settings($telegram->user->id, 'expchat_points', $newpoints);
			$pokemon->settings($telegram->user->id, 'expchat_timeout', time() + 60);
		}
	}
}

if($telegram->text_has("mi experiencia")){
	$points = $pokemon->settings($telegram->user->id, 'expchat_points');
	if(empty($points)){ $points = 0; }
	$level = expchat_level($points);
	$telegram->send
		->text("L" .$points ." / $points EXP")
	->send();
	return -1;
}

?>
