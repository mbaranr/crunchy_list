<div class="container white-bg black-text padding-v-15 round-border">
	<div class="text-center"><span class="bold"><?php echo count($animes); ?> animes</span> match your filter settings</div>
	<div class="animes-container padding-15">
    <?php
    foreach($animes as $anime) {
        echo '<div class="anime-box margin-auto">';
        echo '<div class="anime-cover margin-auto">';
        echo '<a href="?route=anime&id=' . $anime->getId() . '">';
        echo '<img src="' . $anime->getImg() . '" width="200" height="300">';
        echo '</a>';
        echo '<a href="./"><form method="post"><button type="submit" name="toggle-favorite" value="' . $anime->getId() . '" class="emoji-button">➖</button></form></a>';
        echo '</div>';
        echo '<a href="?route=anime&id=' . $anime->getId() . '">';
        echo '<div class="anime-title">' . $anime->getTitle() . '</div>';
        echo '</a>';
        echo '<div class="star">⭐ </div><div class="rating"> ' . round($anime->getAverageRating(), 2) . '</div>';
        echo '</div>';
    }
    ?>
    </div>
</div>