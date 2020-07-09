<?php /*%%SmartyHeaderCode:379650f1dacd9be7d2-73989818%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '940808df3e10be4fe223c3fcda4f5cf04b435104' => 
    array (
      0 => 'C:\\xampp\\htdocs\\GGC_Framework\\application\\Test\\view\\http\\template\\smarty_template\\templates\\html\\v_login.tpl',
      1 => 1358527604,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '379650f1dacd9be7d2-73989818',
  'cache_lifetime' => 3600,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_518b5079efa1f9_11248506',
  'has_nocache_code' => false,
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_518b5079efa1f9_11248506')) {function content_518b5079efa1f9_11248506($_smarty_tpl) {?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
		<form id="form_479068" class="appnitro"  method="post" action="/GGC_Framework/application/Test/index.php?GGC_Entity=login">
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
</html><?php }} ?>