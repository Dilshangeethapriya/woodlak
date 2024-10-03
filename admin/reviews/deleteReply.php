<?php
session_start();
include '../../config/dbconnect.php'; 

if (isset($_POST['delete_reply'])) {
    $replyID = intval($_POST['replyID']);
    
    $deleteReplyQuery = $conn->prepare("DELETE FROM reviewreply WHERE replyID = ?");
    $deleteReplyQuery->bind_param('i', $replyID);
    
    if ($deleteReplyQuery->execute()) {
        header("Location: view_reviews.php?PRODUCTC=" . $_REQUEST['PRODUCTC']);
        exit();
    } else {
        echo "Error deleting reply: " . $conn->error;
    }
}
?>
