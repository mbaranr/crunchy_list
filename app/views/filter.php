<div class="container bright-grey-bg black-text padding-v-15 round-border">
    <div class="margin-h-15">
		<div class="filter-toggle-button" onclick="toggleForm()">&#9650; Hide Filter &#9650;</div>
		<form class="form-container margin-h-15" method="post" name="filter-form">
			<input type="hidden" name="filter-form" value=""/>
            <div class="input-row">
                <div class="input-column width-50">
                    <label for="anime-title" class="input-label">Anime Title:</label>
                    <input type="text" id="anime-title" name="anime-title" placeholder="Enter anime title" class="input-field round-border padding-5" value="<?php if(!empty($filter->getTitle())){echo $filter->getTitle();} ?>">
                </div>
                <div class="input-column width-50">
                    <label for="min-rating-count" class="input-label">Min. Rating Count:</label>
                    <input type="number" id="min-rating-count" name="min-rating-count" min="0" max="500" step="5" value="<?php echo $filter->getMinRatingCount(); ?>" class="input-field round-border padding-5">
                    <label for="order-by" class="input-label">Order by:</label>
                    <select id="order-by" name="order-by" class="input-field round-border padding-5">
                        <option value="rating-asc"<?php if($filter->getOrderBy() == 'rating-asc'){ echo ' selected';} ?>>Rating &#9650;</option>
                        <option value="rating-desc"<?php if($filter->getOrderBy() == 'rating-desc'){ echo ' selected';} ?>>Rating &#9660;</option>
                    </select>
                </div>
            </div>
            <div class="checkbox-container">
            	<select id="genre-filter-behavior" name="genre-filter-behavior" class="input-field round-border padding-5">
                    <option value="and"<?php if($filter->getGenreFilterBehavior() == 'and'){ echo ' selected';} ?>>AND (All selected)</option>
                    <option value="or"<?php if($filter->getGenreFilterBehavior() == 'or'){ echo ' selected';} ?>>OR (min. 1 selected)</option>
                </select>
                <?php
                foreach($genres as $genre) {
                    $chkString = ($gc->ifGenreIdInArray($filter->getGenres(), $genre->getId())) ? ' checked' : '';
                    echo '<label><input type="checkbox" name="genres[]" value="' . $genre->getId() . '"' . $chkString . '> ' . $genre->getName() . '</label>';
                }
                ?>
            </div>
            <div class="text-center">
                <button type="submit" class="filter-button round-border">Filter</button>
                <button type="button" class="filter-button round-border" onclick="resetForm()">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleForm() {
    const formContainer = document.querySelector('.form-container');
    const toggleButton = document.querySelector('.filter-toggle-button');
    formContainer.classList.toggle('collapsed');
    if (formContainer.classList.contains('collapsed')) {
        toggleButton.style.marginBottom = '0';
        toggleButton.innerHTML = '&#9660; Show Filter &#9660;'; // Text for collapsed state
    } else {
        toggleButton.style.marginBottom = '10px';
        toggleButton.innerHTML = '&#9650; Hide Filter &#9650;'; // Text for expanded state
    }
}

function resetForm() {
    // Define default values
    const defaultValues = {
        'anime-title': '', // Default value for anime title
        'min-rating-count': 30, // Default value for minimum rating count
        'order-by': 'rating-desc', // Default value for order by
        'genre-filter-behavior': 'or', // Default value for genre filter behavior
        'genres': [] // Default value for genres
    };

    const form = document.querySelector('form[name="filter-form"]');
    
    // Reset text and number fields to default values
    form.querySelector('#anime-title').value = defaultValues['anime-title'];
    form.querySelector('#min-rating-count').value = defaultValues['min-rating-count'];
    form.querySelector('#order-by').value = defaultValues['order-by'];
    form.querySelector('#genre-filter-behavior').value = defaultValues['genre-filter-behavior'];

    // Reset checkboxes to default values
    const checkboxes = form.querySelectorAll('input[name="genres[]"]');
    checkboxes.forEach((checkbox) => {
        checkbox.checked = defaultValues['genres'].includes(parseInt(checkbox.value));
    });
}
</script>
