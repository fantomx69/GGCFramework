<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Login</title>
<link rel="stylesheet" type="text/css" href="web/css/login/view.css" media="all">
<script type="text/javascript" src="web/js/login/view.js"></script>

</head>
<body id="main_body" >
	
	<img id="top" src="top.png" alt="">
	<div id="form_container">
	
		<h1><a>Login</a></h1>
		<form id="form_479068" class="appnitro"  method="post" action="{$smarty.server.PHP_SELF}?GGC_Entity=login">
					<div class="form_description">
			<h2>Login</h2>
			<p>Dati di Login.</p>
		</div>						
			<ul >
			
					<li id="li_1" >
		<label class="description" for="element_1">Utente </label>
		<div>
			<input id="element_1" name="Name" class="element text medium" type="text" maxlength="255" value=""/> 
		</div><p class="guidelines" id="guide_1"><small>Nome Utente</small></p> 
		</li>		<li id="li_2" >
		<label class="description" for="element_2">Password </label>
		<div>
			<input id="element_2" name="Password" class="element text medium" type="text" maxlength="255" value=""/> 
		</div><p class="guidelines" id="guide_2"><small>Password</small></p> 
		</li>
			
					<li class="buttons">
			    <input type="hidden" name="form_id" value="479068" />
                <input type="hidden" name="GGC_Entity" value="login" />
			    
				<input id="saveForm" class="button_text" type="submit" name="login" value="Invia" />
		</li>
			</ul>
		</form>	
		
	</div>
	<img id="bottom" src="web/img/login/bottom.png" alt="">
	</body>
</html>