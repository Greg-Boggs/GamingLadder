<?php
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';

require('./../top.php');
?>
<p class="header">News articles.</p>
<?php
if (isset($_GET['read']) && $_GET['read']) {
    $sql = "SELECT * FROM $newstable WHERE news_id = '" . intval($_GET['read']) . "' ORDER BY news_id DESC";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_array($result);
    $news = nl2br($row["news"]);
    ?>
    <p class="text"><b><?php echo $row['date'] . "</b> - <b>" . $row['title'] ?></b></p>
    <p class="text"><?php echo "$news" ?></p>
    <hr size="1"/>
    <?php
}
?>
<p><a href="news-post.php">Post News</a></p>
<table border="1" cellspacing="1" cellpadding="2">
    <tr>
        <td align='center'>View</td>
        <td align='center'>Edit</td>
        <td align='center'>Delete</td>
        <td align='left'><p class='text'><b>Article</b></p></td>
    </tr>
    <?php
    $sql = "SELECT * FROM $newstable ORDER BY news_id DESC";
    $result = mysqli_query($db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        ?>
        <tr>
            <td align='center'><a href='news-view.php?read=<?php echo $row['news_id'] ?>'><img border='1'
                                                                                               src='../images/view.gif'
                                                                                               width='18' height='18'
                                                                                               align='middle'
                                                                                               alt="View"/></a></td>
            <td align='center'><a href='news-edit.php?edit=<?php echo $row['news_id'] ?>'><img border='1'
                                                                                               src='../images/edit.gif'
                                                                                               width='18' height='18'
                                                                                               align='middle'
                                                                                               alt="Edit"/></a></td>
            <td align='center'><a href='news-delete.php?edit=<?php echo $row['news_id'] ?>'><img border='1'
                                                                                                 src='../images/delete.gif'
                                                                                                 width='18' height='18'
                                                                                                 align='middle'
                                                                                                 alt="Delete"/></a></td>
            <td align='left'><p class='text'><?php echo $row['date'] . " - " . $row['title'] ?></p></td>
        </tr>
        <?php
    }
    ?>
</table>
<?php
require('./../bottom.php');
?>
