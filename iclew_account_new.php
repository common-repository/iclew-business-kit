<div class="wrap">
    <?php    echo "<h2>" . __( 'iClew Business Kit Settings', 'iclew_trdom' ) . "</h2>"; ?>     
    <form name="iclew_form" method="post" action="https://iclew.com/site/reg">
        <p>Click the button below to register <i><?php echo $_SERVER['HTTP_HOST']; ?></i> with iClew. The plugin will be then set up automatically. It's simple, easy, and free.</p>
        <p>After registration, the plugin regularly sends information about your Wordpress to iClew server, including the site creation date, plugins or themes installed, and user count. The server uses the information, along with other information collected from the public sources, to prepare personalized improvement recommendations for you. To learn more, check out iClew <a href="https://iclew.com/terms" target="_blank">Terms of Use</a> and <a href="https://iclew.com/privacy" target="_blank">Privacy Policy</a>. </p>
        <input type="hidden" name="cms" value="wordpress">
        <input type="hidden" name="domain" value="<?php echo $_SERVER['HTTP_HOST']; ?>">
        <input type="submit" name="submit" id="submit" class="button button-primary" value="Sign up with iClew" />        
    </form>
</div>