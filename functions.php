<?php
// Truncate long text
function limit_text($text, $limit) {
	if ($text == ""){
		$text = "No description provided.";
		return $text;
	} else {
		$text = htmlentities(preg_replace( "/\r|\n/", "", $text ));
		if (str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos = array_keys($words);
			$text = substr($text, 0, $pos[$limit]) . '...';
		}
		return $text;			
	}
}

// Extract repo name from URL
function get_repo_name($url) {
	$path = parse_url($url, PHP_URL_PATH);
	$segments = explode('/', $path);
	return $segments[2];
}