<?php

$logo_version = explode(".",$_SESSION['version']);

$img_src = "../logos/".$logo_version[0]."_white.png";

?>
<image src="<?= $img_src ?>" style="margin:5px; width:30px; height:30px" title="This is the <?= $logo_version[0] ?> release of Collector" id='version_logo'>
<script>
version = "<?= $_SESSION['version'] ?>";
</script>