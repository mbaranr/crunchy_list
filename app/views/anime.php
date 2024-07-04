<?php
$isFavorite = $fc->isFavorite($anime->getId(), $_SESSION['userID']);
?>

<div class="container white-bg black-text padding-v-20 round-border">
	<div class="padding-h-20 flex">
		<div class="anime-details-cover">
			<img src="<?php echo $anime->getImg(); ?>" width="450" height="675" alt="<?php echo $anime->getTitle(); ?> Cover">
		</div>
		<div class="anime-details-infos">
    		<h1><?php echo $anime->getTitle(); ?></h1>
    		
            <table class="margin-v-20">
                <tbody>
                    <tr>
                        <th>Avg. Rating:</th>
                        <td><?php echo $anime->getAverageRating(); ?><span style="text-shadow: 1px 1px 2px #000;">⭐</span></td>
                    </tr>
                    <tr>
                        <th>Genres:</th>
                        <td><?php echo $gc->getGenresAsText($gc->sortGenresByName($anime->getGenres())); ?></td>
                    </tr>
                    <tr>
                        <th>Episodes:</th>
                        <td><?php echo $anime->getEpisodes(); ?></td>
                    </tr>
                    <tr>
                        <th>Votes:</th>
                        <td><?php echo $anime->getVoteCount(); ?></td>
                    </tr>
                    <tr>
                        <th>Weight:</th>
                        <td><?php echo $anime->getWeight(); ?></td>
                    </tr>
                    <tr>
                        <th>Actions:</th>
                        <td>
                        	<a href="<?php echo $anime->getUrl(); ?>" target="_blank"><span class="play-button">&#9654;&#xFE0E;</span> Watch on Crunchyroll!</a>
                        	<span class="margin-h-15">|</span>
							<span id="toggle-favorite-label">
								<form method="post" id="toggle-favorite-form" style="display: inline;">
									<button type="submit" id="toggle-favorite-button" name="toggle-favorite" value="<?php echo $anime->getId(); ?>" class="toggle-favorite-button">
										<?php if ($isFavorite) { echo '➖'; } else { echo '➕'; } ?>
									</button>
									<?php if ($isFavorite) { echo 'Remove from'; } else { echo 'Add to'; } ?> favorites
								</form>
							</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div id="column-chart" class="anime-details-chart"></div>
        </div>
	</div>
</div>

<script type="text/javascript">
    google.charts.load('current', { packages: ['corechart'] });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ['# of ratings', 'votes', { role: 'annotation' }],
            ['1 ⭐', <?php echo $anime->getRating(1); ?>, '<?php echo $anime->getRating(1); ?>'],
            ['2 ⭐', <?php echo $anime->getRating(2); ?>, '<?php echo $anime->getRating(2); ?>'],
            ['3 ⭐', <?php echo $anime->getRating(3); ?>, '<?php echo $anime->getRating(3); ?>'],
            ['4 ⭐', <?php echo $anime->getRating(4); ?>, '<?php echo $anime->getRating(4); ?>'],
            ['5 ⭐', <?php echo $anime->getRating(5); ?>, '<?php echo $anime->getRating(5); ?>']
        ]);

        var options = {
            title: 'Votes',
            legend: { position: 'none' },
            hAxis: {
                minValue: 0
            },
            colors: ['#FFA500'],
            annotations: {
                alwaysOutside: true,
                textStyle: {
                    fontSize: 18,
                    color: '#000',
                    auraColor: 'none'
                }
            }
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('column-chart'));
        chart.draw(data, options);
    }

    // Add event listener to redraw chart on window resize
    window.addEventListener('resize', drawChart);
</script>
<script>
    document.getElementById('toggle-favorite-label').addEventListener('click', function() {
        document.getElementById('toggle-favorite-button').click();
    });
</script>