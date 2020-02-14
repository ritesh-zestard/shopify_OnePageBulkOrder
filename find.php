<?php
/*
 * This file was developed by Bhavin Shah and It was released under General Public Licence.
 * Author : Bhavin Shah (Magento Ecommerce Certified Developer)
 * Email : bhavinshah.sbs1@gmail.com
 */

ini_set('max_execution_time', 3000000);
ini_set('memory_limit', '1G');

function cleandir($dir) {
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_file($dir . '/' . $file)) {
                if (unlink($dir . '/' . $file)) {
                    
                } else {
                    echo $dir . '/' . $file . ' (file) NOT deleted!<br />';
                }
            } else if ($file != '.' && $file != '..' && is_dir($dir . '/' . $file)) {
                cleandir($dir . '/' . $file);
                if (rmdir($dir . '/' . $file)) {
                    
                } else {
                    echo $dir . '/' . $file . ' (directory) NOT deleted!<br />';
                }
            }
        }
        closedir($handle);
    }
}

//cleandir("var/cache");

function listFolderFiles($dir) {
    $ffs = scandir($dir);
    foreach ($ffs as $ff) {
        if ($ff != '.' && $ff != '..') {
            if (is_dir($dir . '/' . $ff)) {
                listFolderFiles($dir . '/' . $ff);
            } else {
                $file = $dir . '/' . $ff;
                findWord($file, $_POST['word']);
            }
        }
    }
}

function findWord($file, $word) {
    $search = $word;
    $lines = file($file);
    $line_numbers = array();
    $line_number = 0;
    while (list($key, $line) = each($lines)) {
        $line_number = (strpos($line, $search) !== FALSE) ? $key + 1 : 0;
        if ($line_number) {
            $line_numbers[] = $line_number;
        }
    }
    if (count($line_numbers)) {
        echo '<p>' . $file . '<br/>';
        $line_numbers = implode(',', $line_numbers);
        echo '<b>' . $line_numbers . '</b></p>';
    }
}
?>
<script type="text/javascript" src="http://maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css"></script>
<form method="post">

    <p class="checkbox_container"><b>BASE-PATH : </b> <input type="checkbox" name="use" checked><label for="option"><span><?php echo dirname(__FILE__) ?></span></label></p>
    <input type="text" name="search" value="<?php echo $_POST?$_POST['search']:''; ?>" placeholder="please enter path..." /><br/>
    <input type="text" name="word" value="<?php echo $_POST?$_POST['word']:''; ?>" placeholder="please enter word..." />
    <button type="submit" name="submit">Submit</button>
</form>

<?php
if (isset($_POST['submit'])) {
    if (isset($_POST['use'])) {
        $dir = dirname(__FILE__) . '/' . $_POST['search'];
    } else {
        $dir = $_POST['search'];
    }
    echo '<div class="search_result">';
    if (is_dir($dir)) {

        echo "Searching in <b>" . $dir . "</b>";
        echo "<br/>Searching for <b>" . $_POST['word'] . "</b> word<br/><br/>";
        listFolderFiles($dir);
        echo '<br/><div class="done">Done</div>';
    } else {
        echo "<b>No directory found.</b>";
    }
    echo '</div>';
}
?>
<style>
    body {
        margin: 0 auto;
        position: relative;
        font-family: calibri;
        font-size: 18px;
        width: 100%;
    }

    form {
        padding: 30px;
        margin-bottom: 0;
    }

    input[type="text"] {
        width: 250px;
        padding: 5px;
        font-size: 18px;
        margin-bottom: 10px;
    }

    button {
        clear: both;
        float: none;
        display: block;
        width: 250px;
        padding: 10px;
        background: #3f51b5;
        border: none;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }
    .search_result {
        padding: 30px;
        word-break: break-word;
    }
    .done {
    width: 100%;
    padding: 10px;
    text-align: center;
    font-size: 20px;
    background: green;
    color: #fff;
    font-weight: bold;
}

    
/*    
    .checkbox_container {
	width: 20px;	
	margin: 20px auto;
	position: relative;
}

.checkbox_container label {
	cursor: pointer;
	position: absolute;
	width: 20px;
	height: 20px;
	top: 0;
	border-radius: 4px;

	-webkit-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	-moz-box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	box-shadow: inset 0px 1px 1px white, 0px 1px 3px rgba(0,0,0,0.5);
	background: #fcfff4;

	background: -webkit-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -moz-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -o-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: -ms-linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	background: linear-gradient(top, #fcfff4 0%, #dfe5d7 40%, #b3bead 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=0 );
}

.checkbox_container label:after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
	filter: alpha(opacity=0);
	opacity: 0;
	content: '';
	position: absolute;
	width: 9px;
	height: 5px;
	background: transparent;
	top: 4px;
	left: 4px;
	border: 3px solid #333;
	border-top: none;
	border-right: none;

	-webkit-transform: rotate(-45deg);
	-moz-transform: rotate(-45deg);
	-o-transform: rotate(-45deg);
	-ms-transform: rotate(-45deg);
	transform: rotate(-45deg);
}

.checkbox_container label:hover::after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=30)";
	filter: alpha(opacity=30);
	opacity: 0.5;
}

.checkbox_container input[type=checkbox]:checked + label:after {
	-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=100)";
	filter: alpha(opacity=100);
	opacity: 1;
}*/
</style>