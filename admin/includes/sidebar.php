<div class="span4">
    <h3>Remember:</h3>
	<p>
		<?php 
			//just something fun, show a random "quote" on each page load
			$quotes = array("Use the force for good, not evil.", "Code is poetry", "Think hard before you delete something.", "Logout before leaving your PC.", "The cake is a lie.", "Mario can't save you now.", "Today is: ". Date("l F d, Y"), "I like 	<span class='default-font'>&pi;</span>.", "Do a <a href='http://www.google.com/search?q=do+a+barrell+roll&ie=utf-8' target='_blank'>barrel roll</a>!", "Bradley Rosenfeld is awesome!");
			$key = array_rand($quotes, 1);
			echo $quotes[$key];
		?>

	</p>
</div>