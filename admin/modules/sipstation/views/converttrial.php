<?php
if (!empty($key) && $data['account_type'] === 'TRIAL') {
?>
	<script src="/admin/assets/sipstation/js/math-session.js"></script>

	<div id ="ssconvert"></div>

	<script>
		//generate a session id for us
		var ssSession = new Session('ss_session_id');
		$(document).ready(function() {
			var baseURL = "<?php echo $base_url; ?>";
			var body = $("#ssconvert");

			//Since they already have a token, do we use that instead of session?
			$.post(baseURL + "/store/convert",
			{
				session: (ssSession.get() === "") ? ssSession.set() : ssSession.get(),
				token: '<?php echo $key; ?>'
			},
			function(data) {
				if (data.status) {
					body.html($.parseJSON(data.message));
				}
			});
		});
	</script>
<?php
} else {
	header('Location: /admin/config.php?display=sipstation');
}
