

<?php if (substr(URL, 0, 8) !== 'account/' && !in_array(URL, ['', 'register', 'print/cards'])) { ?>
    <hr>
    <div class="print">
        <p>Print of https://thespacewar.com/<?=URL?></p>
    </div>
    <div class="footer no-print">
        <?php if (!isset($logged_in) || $logged_in == []) { ?>
            <p>Enter a username to play for free:</p>
            <form method="post" action="/register">
                <input type="text" name="username" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9]+" placeholder='Username' title="Numbers or letters only. Minimum 3 characters.">
                <input type="submit" name="check" value="Play">
            </form>
        <?php } else { ?>
            <p></p>
        <?php } ?>
    </div>
    <div style="clear:both;"> </div>
<?php } ?>

<?php if ((URL != 'cards/' && strpos(URL, 'cards/', 0) === 0) || (URL != 'commanders/' && strpos(URL, 'commanders/', 0) === 0)) {
    echo '</div>';
}
?>



</div>

</div>

<?php if (URL != 'print/cards') { ?>
    <p class='no-print' style="text-align: center;">&copy; TheSpaceWar.com | <a href="/credits">Full Credits</a><br>Loaded in <?= round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 2) ?> seconds</p>
<?php } ?>


<?php if($_SERVER['HTTP_HOST'] == 'thespacewar.com') { ?>
<script>
(function() { // Redistats, track version 1.0
    var global_id = 2; // Global ID, don't change this.
    var property_id = 43; // Property ID
    var url = encodeURIComponent(window.location.href.split('#')[0]);
    var referrer = encodeURIComponent(document.referrer);
    var x = document.createElement('script'), s = document.getElementsByTagName('script')[0];
    x.src = '//redistats.com/track.js?gid='+global_id+'&pid='+property_id+'&url='+url+'&referrer='+referrer;
    s.parentNode.insertBefore(x, s);
})();
</script>
<?php } ?>


<?php
// Activate in 2021? 
//if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) != 'en') include(ROOT.'view/google-translate.php') ?>



</body>
</html>