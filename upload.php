<?php 
  
  $bvh = $_FILES['uploaded_file']['tmp_name'];
  $myfile = fopen($bvh, "r") or die("Unable to open file!");
  print_r(fread($myfile,filesize($bvh)));
  fclose($myfile);
      
    
?>