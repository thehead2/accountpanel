<?php
	if (!isset($load)) exit;
	
	if ($validate->blank($player['email'])) exit(header("Location: ?page=server"));
?>
