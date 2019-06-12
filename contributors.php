<?php

// ! DELIBERATELY NOT USING FILESYSTEM FOR THIS (see homepage index.php)//
require("../sqlConnect.php");

$sql = "SELECT * FROM `hall_of_fame`";	
$result = $conn->query($sql);
$contributors = [];
while($row = $result->fetch_assoc()) {
	$contributors[$row["order"]] = $row;
}

?>

<style>
.image-wrapper {
    width: 100%;
    height: 100%;
    background-color: #000;
    border-radius: 50%;
    margin: 0;
    padding: 0;
}
.ppimg {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
    display: block;
    border-radius: 50%;
}
</style>

<em>In Chronological order</em>
<table class='table'> <!--table-striped-->
	<!--
	<thead class="thead-light">
		
		<tr>
			<th scope="col">Name</th>
			<th scope="col">Pic</th>
			<th scope="col">Blurb</th>
			<th scope="col">Contributions</th>
		</tr>
	</thead>
	-->
	<tbody>
	<?php
		for($i = 0; $i < count($contributors); $i ++){
			$name   = $contributors[$i]['name'];
			$pic    = $contributors[$i]['pic_address'];
			$blurb  = $contributors[$i]['blurb'];
		
			?><tr>										
					<?php 
						if($pic == ''){
							echo "<td></td>";
						} else {
							echo "<td><img class='ppimg' style='width:100px; height:100px;' src='$pic'/></td>";
						}
					?>
					<td><em><b><?= $name ?></b></em></td>
					
					<td><?= $blurb ?></td>		
					<!--
					<td> Here is where we'll summarise contributions like trialtypes published, experiments published, etc. </td>
					-->
			  </tr>
		<?php } 
	?>
	</tbody>
</table>






	