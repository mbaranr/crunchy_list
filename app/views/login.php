<div class="centered-container">
    <h1 class="text-center margin-b-40"><?php echo PAGE_TITLE; ?></h1>
    <form action="./" method="POST">
    	<div class="login-container white-bg black-text padding-20 margin-auto flex round-border">
    		<?php if ($logger->hasError()) echo '<div class="red-text">' . $logger->getError() . '</div>'; ?>
    		<?php if ($logger->hasInfo()) echo '<div class="green-text">' . $logger->getInfo() . '</div>'; ?>
            <!-- create entries for username and password -->
            <div class="margin-5"><input type="text" class="login-input padding-h-20" name="username" placeholder="Username"<?php if(!empty($_POST['username'])){echo ' value="' . $_POST['username'] .'"';} ?>></div>
            <div class="margin-5"><input type="password" class="login-input padding-h-20" name="password" placeholder="Password"></div>
            <div class="margin-5"><button type="Login" class="login-button" name="login">Login</button></div>
            <!-- provide a hyperlink to the register page -->
            <p>Don't have an account? <a href='?route=register'>Register here</a></p>
    	</div>
    </form>
</div>