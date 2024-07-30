<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Vote System</title>
		<meta charset="utf-8" />
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="stylesheet" href="css/reset.css" type="text/css" />
		<link rel="stylesheet" href="css/base.css" type="text/css" />
		<link rel="stylesheet" href="css/jqueryUI.css" type="text/css" />
		<style type="text/css">
			body {
				overflow:hidden;
			}
			
			.highlight {
				color: red !important;
				font-weight: bold;
				text-align: center;
				display: block;
				padding: 10px 0px;
			}
			
			.loader {
				color: black !important;
				display: block;
				width: 100%;
				text-align: center;
				margin-top: 250px;
			}
		</style>
		<script src="libraries/jquery.js"></script>
		<script src="libraries/jqueryUI.js"></script>
		<script type="text/javascript" language="javascript">
			var voteLoaded = 0;
			$(document).ready(function() {
				$("#voteFrame").load(function(){
					if (voteLoaded >= 1)
					{
						window.opener.processVote();
						$(this).after("<span class='loader'><img src='images/wait.gif' style='vertical-align:middle' /> Please wait... Processing your vote...</span>");
						$(this).remove();
					}
					else
						voteLoaded++;
				});
			});
		</script>
	</head>
	<body>
		<span class="highlight">Do not rush to close the window after you've succesfully voted. It will close automatically.</span>
		<iframe id="voteFrame" src="<?php echo base64_decode($_GET['url']); ?>" sandbox="allow-forms allow-scripts"></iframe>
	</body>
</html>