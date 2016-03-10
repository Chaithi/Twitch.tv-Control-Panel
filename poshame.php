<?php
    // Constants
    define("SHOWNAME", "enter-name-here"); // Name of the Channel
    define("LOGO", "enter-logo-here"); // Logo to appear at the top. 200x200. Can modify size in the stylesheet.
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="stylesheet.css" type="text/css" media="screen" charset="utf-8">
        <link href='https://fonts.googleapis.com/css?family=VT323' rel='stylesheet' type='text/css'> <!-- Specific font for our show. Modify to fit your theme.-->
        <title><? echo SHOWNAME; ?> Control Panel</title>
    </head>
    <body>
        <?php
            include 'tools.php';
            $details = getChannelDetails(); // Obtains details about the channel.
            $title = $details['title']; // Gets the current Status value of the channel.
            $game = $details['game']; // Gets the current Game value of the channel.
            $followers = $details['followers']; // Gets the current number of Followers of the channel.
            $online = $details['online']; // Determines if the channel is currently streaming.
            $viewers = $details['viewers']; // If streaming, determines number of viewers. If not, set to 0.
        ?>
        <img src='<?php echo LOGO; ?>' class="logo"> <!-- Display logo -->
        <h1><? echo SHOWNAME; ?> Control Panel</h1>
        <?php if (!empty($_POST)): ?> <!-- If the form has been submitted, attempt to set the channel info and notify of the new title and game -->
            <?php setChannelTitle($_POST["newTitle"], $_POST["newGame"]); ?>
            <p><label>Title set to</label> <?php echo htmlspecialchars($_POST["newTitle"]); ?>!<br></p>
            <p><label>Game set to</label> <?php echo htmlspecialchars($_POST["newGame"]); ?>.<br></p>
        <?php endif; ?>
        <!-- Gather the new title and game and submit in order to set the info to the channel -->
        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> method="post">
            <label>Title:</label> <input type="text" name="newTitle"><br>
            <label>Game:</label> <input type="text" name="newGame"><br>
            <input type="submit">
        </form>
        <br>
        <!-- Provide the pulled channel info. Title, game, # of Followers, Online, Viewers -->
        <label>Current title:</label> <?php echo $title; ?><br>
        <label>Current game:</label> <?php echo $game; ?><br>
        <label># of Followers:</label> <?php echo $followers; ?><br>
        <label>Online:</label> <?php if ($online == false) { echo "No"; } else { echo "Yes"; } ?><br>
        <label>Viewers:</label> <?php echo $viewers; ?><br>
        <label>Recent Broadcasts:</label><br>
        <?php
            $videos = getVideos();
            foreach ($videos['videos'] as $video)
            {
                echo "<div class='video'>";
                echo "<label>" . $video['title'] . "</label><br>";
                echo "<a href='" . $video['url'] . "'><img src='" . $video['preview'] . "'></a><br>";
                echo "<label>Length: </label>" . gmdate("H:i:s", $video['length']) . "<br>";
                echo "<label>Views: </label>" . $video['views'];
                echo "</div>";
            }
        ?>
        <br>
        <label>Recent Highlights:</label><br>
        <?php
            $videos = getHighlights();
            foreach ($videos['videos'] as $video)
            {
                echo "<div class='video'>";
                echo "<label>" . $video['title'] . "</label><br>";
                echo "<a href='" . $video['url'] . "'><img src='" . $video['preview'] . "'></a><br>";
                echo "<label>Length: </label>" . gmdate("H:i:s", $video['length']) . "<br>";
                echo "<label>Views: </label>" . $video['views'];
                echo "</div>";
            }
        ?>
        <br>
        <label>Followers:</label><br>
        <?php
            $followers = getFollowers();
            foreach ($followers['follows'] as $follower)
            {
                if (!$follower['user']['logo'] == null)
                {
                    echo '<img src="' . $follower['user']['logo'] . '">';
                }
                echo '<a href="' . $follower['user']['_links']['self'] . '">' . $follower['user']['display_name'];
                echo '<br>';
            }
        ?>
    </body>
</html>