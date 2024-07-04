<div class="centered-container">
    <h1 class="text-center margin-b-40"><?php echo PAGE_TITLE; ?></h1>
    <form method="POST">
    	<div class="register-container white-bg black-text padding-20 margin-auto flex round-border">
    		<?php if ($logger->hasError()) echo '<div class="red-text">' . $logger->getError() . '</div>'; ?>
            <!-- create entries for username and password -->
            <div class="margin-5"><input type="text" class="register-input padding-h-20" name="username" placeholder="Username"<?php if(!empty($_POST['username'])){echo ' value="' . $_POST['username'] .'"';} ?>></div>
            <div class="margin-5"><input type="password" class="register-input padding-h-20" name="password" placeholder="Password"></div>
            <div class="margin-5"><input type="password" class="register-input padding-h-20" name="repeat" placeholder="Repeat Password"></div>
            <div class="margin-5"><button type="submit" class="register-button" name="register">Register</button></div>
            <p>Already have an account? <a href='?route=login'>Login here</a></p>
    	</div>
    </form>
</div>