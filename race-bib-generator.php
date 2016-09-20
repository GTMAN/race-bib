<?php

/*
Plugin Name: Race Bib Generator
Description: Generate custom race bibs with numbers and names
Version: 1.0.0
Author: Bill Ott
License: GPL
*/

require_once(ABSPATH.'wp-admin/includes/file.php');


add_shortcode( 'bib-generator', 'rb_get_bib_info' );

/* Runs when plugin is activated */
register_activation_hook(__FILE__,'race_bib_generator_install'); 

/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'race_bib_generator_remove' );

function race_bib_generator_install() {
/* Creates new database field */
add_option("race_bib_generator_data", 'Default', '', 'yes');
}

function race_bib_generator_remove() {
/* Deletes the database field */
delete_option('race_bib_generator_data');
}



function rb_get_bib_info() {
?>
<div>
<h2>Race Bib Info</h2>
<form method="post">
<table id=bib_gen>
<tr><td>Bib Number</td><td><input size=5 type="text" name="bib_num" value="<?php echo sanitize_text_field($_POST['bib_num']) ?>" /></td></tr>
<tr><td>First Name (optional)</td><td><input size=20 type="text" name="bib_name" value="<?php echo sanitize_text_field($_POST['bib_name'])?>" /></td></tr>
<tr><td>Distance</td><td><select name="bib_color">
<option>
 


<?php 
       $path = "/wp-content/plugins/race-bib-generator/bibs/";
        
        foreach( glob( plugin_dir_path( __FILE__ ) . "bibs/*.{png,jpg}" ) as $filename ){
       
        
        $filename = basename($filename);
        
        if ($filename == "Select.png"){

        echo '<option value="'.$path.'Select.png" selected>Select</option>';
         }

        else {
        $lable = substr($filename, 0, -4);  
        $filename = $path.$filename;
          
               echo "<option value='" . $filename . "'>".$lable."</option>";
    }
   }
?>


</select></td></tr>
<tr><td colspan=3><input type="submit" name='rb-submitted' value="Generate" /></td></tr>
</table>
</form>
<div id="new_bib">
<?php
if(isset($_POST["rb-submitted"])){
   rb_show_bib();
}
?>
</div>

<?php 
} 

function test_input($data) {
  $data = sanitize_text_field( $data );
  //$data = trim($data);
 // $data = stripslashes($data);
 // $data = strip_tags($data);
  //$data = htmlspecialchars($data);
  
  return $data;
}

function rb_show_bib(){

// define variables and set to empty values
$n1 = $n2 = $n3 = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $n1 = test_input($_POST["bib_num"]);
  $n2 = test_input($_POST["bib_name"]);
  $n3 = test_input($_POST["bib_color"]);
}

if ($n1 == ""){ 
echo '<script language="javascript">';
echo 'alert("You must enter a Bib Number!")';
echo '</script>';

}



if ($n1 && $n3){

$n2 = strtoupper($n2);

$path = get_home_path();


$background = "$n3";
$font = plugin_dir_path( __FILE__ ).'arialbd.ttf';

$image_path = "$path"."$background";
$image = imageCreateFromPNG("$image_path");
$color = ImageColorAllocate($image, 0,0,0);

// Calculate horizontal alignment for the names.
$NUM = ceil((160 - strlen($n1)*6) / 2);
$NAM = ceil(280 - strlen($n2)*15);

// Write number and name
imagettftext($image, 140, 0, $NUM, 250, $color, $font, $n1);
imagettftext($image, 34, 0, $NAM, 395, $color, $font, $n2);

// Return output.
ob_start();
ImagePNG($image);
ImageDestroy($image);
$data = ob_get_contents ();
ob_end_clean ();
$i = "<a download='bib.png' href='data:image/png;base64,".base64_encode ($data)."'><img src='data:image/png;base64,".base64_encode ($data)."'></a><br><br>Click image to download.";


echo $i;
}
}
?>
