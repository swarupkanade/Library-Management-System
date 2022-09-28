<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "../header.php";
?>

<html>
	<head>
		<title>LMS</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css">
		<link rel="stylesheet" href="css/register_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Member Registration</legend><p>Please fillup the form below:</p></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>

				<div class="icon">
					<input class="m-name" type="text" name="m_name" placeholder="Full Name" required />
				</div>

				<div class="icon">
					<input class="m-email" type="email" name="m_email" id="m_email" placeholder="Email" required />
				</div>
				
				<div class="icon">
					<input class="m-user" type="text" name="m_user" id="m_user" placeholder="Username" required />
				</div>
				
				<div class="icon">
					<input class="m-pass" type="password" name="m_pass" placeholder="Password" required />
				</div>
			
				
				<div class="icon">
					<input class="m-balance" type="number" name="m_balance" id="m_balance" placeholder="Initial Balance" required />
				</div>
				
				<br />
				<input type="submit" name="m_register" value="Submit" />

				<br /><br /><br /><br />
			
				<p align="center" style="font-size: 1.8rem;">Already have an account?&nbsp;<a href="index.php" style="text-decoration:none; color:red;">Login Now!</a>

				<p align="center"><a href="../index.php" style="text-decoration:none; font-size: 1.8rem;">Go Back</a>
		</form>
	</body>
	
	<?php
		if(isset($_POST['m_register']))
		{
			if($_POST['m_balance'] < 100)
				echo error_with_field("Initial balance must be at least 100 in order to create an account", "m_balance");
			else
			{
				$query = $con->prepare("(SELECT username FROM member WHERE username = ?) UNION (SELECT username FROM pending_registrations WHERE username = ?);");
				$query->bind_param("ss", $_POST['m_user'], $_POST['m_user']);
				$query->execute();
				if(mysqli_num_rows($query->get_result()) != 0)
					echo error_with_field("The username you entered is already taken", "m_user");
				else
				{
					$query = $con->prepare("(SELECT email FROM member WHERE email = ?) UNION (SELECT email FROM pending_registrations WHERE email = ?);");
					$query->bind_param("ss", $_POST['m_email'], $_POST['m_email']);
					$query->execute();
					if(mysqli_num_rows($query->get_result()) != 0)
						echo error_with_field("An account is already registered with that email", "m_email");
					else
					{
						$query = $con->prepare("INSERT INTO pending_registrations(username, password, name, email, balance) VALUES(?, ?, ?, ?, ?);");
						$m_pass = md5($_POST['m_pass']);
						$query->bind_param("ssssd", $_POST['m_user'], $m_pass, $_POST['m_name'], $_POST['m_email'], $_POST['m_balance']);
						if($query->execute())
							echo success("Details submitted, soon you'll will be notified after verifications!");
						else
							echo error_without_field("Couldn\'t record details. Please try again later");
					}
				}
			}
		}
	?>
	
</html>