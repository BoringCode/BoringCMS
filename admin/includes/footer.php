      <footer>
        <p><a href="<?php echo siteURL($dbc); ?>" target="_blank">View Site</a></p>
		<p>&copy; <?php echo date("Y"); ?> - Bradley Rosenfeld | <a href="<?php echo adminURL($dbc); ?>credits">Credits</a></p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>

<?php 
mysqli_close($dbc); // Close the database connection.
?>