<?php
include('../frontend/php/DBConnect.php');
header('Content-Type: application/json');

// Line Chart: Posts over time
$dateQuery = "SELECT DATE(created_at) AS date, COUNT(*) AS count FROM posts WHERE is_deleted = 0 GROUP BY DATE(created_at) ORDER BY date";
$dateResult = $conn->query($dateQuery);
$postDates = ['labels' => [], 'counts' => []];
while ($row = $dateResult->fetch_assoc()) {
  $postDates['labels'][] = $row['date'];
  $postDates['counts'][] = (int) $row['count'];
}

// Pie Chart: Posts by category
$catQuery = "SELECT category, COUNT(*) AS count FROM posts WHERE is_deleted = 0 GROUP BY category";
$catResult = $conn->query($catQuery);
$categories = ['labels' => [], 'counts' => []];
while ($row = $catResult->fetch_assoc()) {
  $categories['labels'][] = $row['category'] ?: 'Uncategorized';
  $categories['counts'][] = (int) $row['count'];
}

// Site Usage: Weekly new users, posts, topics, comments, likes
$usageQuery = "
  SELECT
    YEARWEEK(created_at, 1) AS year_week,
    MIN(DATE(created_at)) AS week_start,
    COUNT(CASE WHEN type = 'user' THEN 1 END) AS new_users,
    COUNT(CASE WHEN type = 'post' THEN 1 END) AS new_posts,
    COUNT(CASE WHEN type = 'topic' THEN 1 END) AS new_topics,
    COUNT(CASE WHEN type = 'comment' THEN 1 END) AS new_comments,
    COUNT(CASE WHEN type = 'like' THEN 1 END) AS new_likes
  FROM (
    SELECT created_at, 'user' AS type FROM users
    UNION ALL
    SELECT created_at, 'post' FROM posts WHERE is_deleted = 0
    UNION ALL
    SELECT created_at, 'topic' FROM topics
    UNION ALL
    SELECT created_at, 'comment' FROM comments WHERE is_deleted = 0
    UNION ALL
    SELECT created_at, 'like' FROM likes
  ) AS combined
  GROUP BY year_week
  ORDER BY year_week
";

$usageResult = $conn->query($usageQuery);
$siteUsage = [
  'labels' => [],
  'users' => [],
  'posts' => [],
  'topics' => [],
  'comments' => [],
  'likes' => []
];

if ($usageResult) {
  while ($row = $usageResult->fetch_assoc()) {
    $siteUsage['labels'][] = "Week of " . $row['week_start'];
    $siteUsage['users'][] = (int) $row['new_users'];
    $siteUsage['posts'][] = (int) $row['new_posts'];
    $siteUsage['topics'][] = (int) $row['new_topics'];
    $siteUsage['comments'][] = (int) $row['new_comments'];
    $siteUsage['likes'][] = (int) $row['new_likes'];
  }
} else {
  error_log("SQL error in usageQuery: " . $conn->error);
}

echo json_encode([
  'postDates' => $postDates,
  'categories' => $categories,
  'siteUsage' => $siteUsage
]);

$conn->close();
?>