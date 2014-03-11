<?php require('includes/config.php'); 


$stmt = $db->prepare('SELECT catID,catTitle FROM blog_cats WHERE catSlug = :catSlug');
$stmt->execute(array(':catSlug' => $_GET['id']));
$row = $stmt->fetch();

//if post does not exists redirect user.
if($row['catID'] == ''){
	header('Location: ./');
	exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blog - <?php echo $row['catTitle'];?></title>
    <link rel="stylesheet" href="style/normalize.css">
    <link rel="stylesheet" href="style/main.css">
</head>
<body>

	<div id="wrapper">

		<h1>Blog</h1>
		<p>Posts in <?php echo $row['catTitle'];?></p>
		<hr />
		<p><a href="./">Blog Index</a></p>

		<?php	
		try {

			$stmt = $db->prepare('
				SELECT 
					blog_posts_seo.postID, blog_posts_seo.postTitle, blog_posts_seo.postSlug, blog_posts_seo.postDesc, blog_posts_seo.postDate 
				FROM 
					blog_posts_seo,
					blog_post_cats
				WHERE
					 blog_posts_seo.postID = blog_post_cats.postID
					 AND blog_post_cats.catID = :catID
				ORDER BY 
					postID DESC
				');
			$stmt->execute(array(':catID' => $row['catID']));
			while($row = $stmt->fetch()){
				
				echo '<div>';
					echo '<h1><a href="'.$row['postSlug'].'">'.$row['postTitle'].'</a></h1>';
					echo '<p>Posted on '.date('jS M Y H:i:s', strtotime($row['postDate'])).' in ';

						$stmt2 = $db->prepare('SELECT catTitle, catSlug	FROM blog_cats, blog_post_cats WHERE blog_cats.catID = blog_post_cats.catID AND blog_post_cats.postID = :postID');
						$stmt2->execute(array(':postID' => $row['postID']));

						$catRow = $stmt2->fetchAll(PDO::FETCH_ASSOC);

						$links = array();
						foreach ($catRow as $cat)
						{
						    $links[] = "<a href='c-".$cat['catSlug']."'>".$cat['catTitle']."</a>";
						}
						echo implode(", ", $links);

					echo '</p>';
					echo '<p>'.$row['postDesc'].'</p>';				
					echo '<p><a href="'.$row['postSlug'].'">Read More</a></p>';				
				echo '</div>';

			}

		} catch(PDOException $e) {
		    echo $e->getMessage();
		}

		?>

	</div>


</body>
</html>