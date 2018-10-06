<div id="footer">

    <!-- Checks for Monitorr application update on page load & "Check for update" click: -->
    <script src="assets/js/update.js" async></script>

    <div id="monitorrid">
        <a href="https://github.com/monitorr/monitorr" title="Monitorr GitHub repo" target="_blank"
           class="footer">Monitorr </a> |
        <a href="settings.php" title="Monitorr Settings" target="_blank" class="footer">Settings</a> |
        <a href="https://github.com/Monitorr/monitorr/releases" title="Monitorr releases" target="_blank" class="footer">
            Version: <?php echo file_get_contents("assets/js/version/version.txt"); ?></a>
        <br>
    </div>

</div>

</body>

</html>
