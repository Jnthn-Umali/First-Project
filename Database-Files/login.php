
<?php
if(isset($_POST['btnlogin']))
{
	require_once "config.php";

	$sql = "SELECT * FROM tblaccounts WHERE username = ? AND password = ? AND userstatus = 'ACTIVE'";

	if($stmt = mysqli_prepare($link, $sql))
	{
		//bind the data from the login form to the sql statement
		mysqli_stmt_bind_param($stmt, "ss", $_POST['txtusername'], $_POST['txtpassword']);

		if(mysqli_stmt_execute($stmt))
		{
			$result = mysqli_stmt_get_result($stmt);
			if(mysqli_num_rows($result) > 0)
			{
				$account = mysqli_fetch_array($result, MYSQLI_ASSOC);
				session_start();
				$_SESSION['username'] = $_POST['txtusername'];
				$_SESSION['usertype'] = $account['usertype'];
				header("location: index.php");
			}
			else
			{
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<br>";
				echo "<font color = 'red'> Incorrect login details or account is disable/inactive </font>";
			}
		}
	}	
	else
	{
		echo "Error on login statement";
	}
}
?>
<!DOCTYPE html>
   <head>
      <meta charset="utf-8">
      <title>Arellano University Subject Advising System Login</title>
      <link rel="stylesheet" href="bootstrap.css">
      <style>
      :root{
			--bg-color: #ffffff;
			--text-color: #121212;
			--main-font: 2.2rem;
			--p-font: 1.1rem;
		}
      	header {
            width: 100%;
			top: 0;
			right: 0;
			position: fixed;
			background: #829bed;
			box-shadow: 0px 2px 18px 0 rgb(129 162 182 / 20%);
			text-align: center;
			justify-content: space-between;
			padding: 10px 8%;
			transition: .3s


        }
        .header{
			position: absolute;
			top: 174px;
			width: 80%;
			margin-left: 4%;
		}

     	.navbar{
			display: flex
			text-align: center;
		}
		.navbar a{
			font-size: var(--p-font);
			color: var(--text-color);
			font-weight: 600;
			padding: 5px 10px;
			margin: 0 10px;
			transition: all .40s ease;
		}
		li a.active{
			background-color: #F1D9A7
		}
		.navbar a:hover{
			background: var(--text-color);
			color: #fff
		}


        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            border-radius: 10px 10px 0 0;
            text-align: center;
			padding: 5px;
			background: #829bed

        }
        footer p{
        		color: var(--text-color);
				font-size: 15px;
				font-weight: 600;
				letter-spacing: 2px;
        }
html,body{
  display: grid;
  height: 100%;
  width: 100%;
  place-items: center;
  background: #f2f2f2;
  /* background: linear-gradient(-135deg, #c850c0, #4158d0); */
}
::selection{
  background: #4158d0;
  color: #fff;
}
.wrapper{
	width:400px;
	padding:2rem 1rem;
	margin:25px auto;
	background color: #fff;
	border-radius: 10px;
	text-align: center;
	box-shadow: 0 20px 35px rgba(0, 0, 0, 0.1);
}
.wrapper .title{
  font-size: 35px;
  font-weight: 600;
  text-align: center;
  line-height: 100px;
  color: #fff;
  user-select: none;
  border-radius: 15px 15px 0 0;
  background: linear-gradient(-135deg, #c850c0, #4158d0);
}
.wrapper form{
  padding: 10px 30px 50px 30px;
}
.wrapper form .field{
  height: 50px;
  width: 100%;
  margin-top: 20px;
  position: relative;
}
.wrapper form .field input{
  height: 100%;
  width: 100%;
  outline: none;
  font-size: 17px;
  padding-left: 20px;
  border: 1px solid lightgrey;
  border-radius: 25px;
  transition: all 0.3s ease;
}
.wrapper form .field input:focus,
form .field input:valid{
  border-color: #4158d0;
}
.wrapper form .field label{
  position: absolute;
  top: 50%;
  left: 20px;
  color: #999999;
  font-weight: 400;
  font-size: 17px;
  pointer-events: none;
  transform: translateY(-50%);
  transition: all 0.3s ease;
}
form .field input:focus ~ label,
form .field input:valid ~ label{
  top: 0%;
  font-size: 16px;
  color: #4158d0;
  background: #fff;
  transform: translateY(-50%);
}
form .content{
  display: flex;
  width: 100%;
  height: 50px;
  font-size: 16px;
  align-items: center;
  justify-content: space-around;
}
form .content .checkbox{
  display: flex;
  align-items: center;
  justify-content: center;
}
form .content input{
  width: 15px;
  height: 15px;
  background: red;
}
form .content label{
  color: #262626;
  user-select: none;
  padding-left: 5px;
}
form .content .pass-link{
  color: "";
}
form .field input[type="submit"]{
  color: #fff;
  border: none;
  padding-left: 0;
  margin-top: -10px;
  font-size: 20px;
  font-weight: 500;
  cursor: pointer;
  background: linear-gradient(-135deg, #c850c0, #4158d0);
  transition: all 0.3s ease;
}
form .field input[type="submit"]:active{
  transform: scale(0.95);
}
form .signup-link{
  color: #262626;
  margin-top: 20px;
  text-align: center;
}
form .pass-link a,
form .signup-link a{
  color: #4158d0;
  text-decoration: none;
}
form .pass-link a:hover,
form .signup-link a:hover{
  text-decoration: underline;
}
div {
  text-align: justify;
  text-justify: inter-word;
}
hr {
  border: 2px solid black;
}

      </style>
   </head>
   <body>
   	<header>
   		<h1>
   		<img src="logo.png" width="80" height="80" align="center"> Arellano University Subject Advising System </img>
        </h1>
    </header>
      
      <div class="wrapper">
         <div class="title">
            AUSAS LOGIN FORM
         </div>
         <form action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method = "POST">
            <div class="field">
               <input type="text" name = "txtusername" required>
               <label>Username</label>
            </div>
            <div class="field">
               <input type="password" name = "txtpassword" required>
               <label>Password</label>
            </div>
            <br>
            <div class="field">
               <input type="submit" name = "btnlogin" value="Login">
            </div>

         </form>
   </body>
</html>

 <footer>
        <p> Copyright © 2024 AUSAS. By Rafael Villena</p>
 </footer>