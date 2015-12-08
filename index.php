<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'common.php';
require_once 'functions.php';

//Check if sort order is set
if(isset($_POST["sort"])){
	$sort_order = $_POST["sort"];
	$sort_split= explode('_', $sort_order);
	$sort = $sort_split[0];
	$order = $sort_split[1];
} else {
	$sort_order = HW_GITHUB_SORT . '_' . HW_GITHUB_ORDER;
	$sort = HW_GITHUB_SORT;
	$order = HW_GITHUB_ORDER;
}

//Build an array of projects
$cache->cache_time = HW_REPOS_CACHE_TIME;
if($repos = $cache->get_cache('repos')){
	$repos_json = $repos;
	$repos_array = json_decode($repos_json, true);
} else {
	$repos = $paginator->fetchAll($client->api('user'), 'repositories', array(HW_GITHUB_USER));
	$repos_array = array();
	foreach($repos as $repo){
		$repos_array[$repo["name"]] = array("description" => $repo["description"], "url" => $repo["html_url"]);
	}
	$repos_json = json_encode($repos_array);
	$cache->set_cache('repos', $repos_json);
}	
	
//Build an array of issues
$cache->cache_time = HW_ISSUES_CACHE_TIME;
if($issues = $cache->get_cache('issues_' . $sort_order)){
	$issues = json_decode($issues, true);
} else {
	$q = 'user:' . HW_GITHUB_USER . ' label:"' . HW_GITHUB_LABEL . '" state:' . HW_GITHUB_STATE;
	$issues = $paginator->fetchAll($client->api('search'), 'issues', [$q, $sort, $order]);
	$issues_json = json_encode($issues);
	$cache->set_cache('issues_' . $sort_order, $issues_json);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo HW_GITHUB_LABEL; ?> @ <?php echo HW_GITHUB_USER; ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
	<link href='//fonts.googleapis.com/css?family=Raleway:400,700|Open+Sans:300,600' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="styles.css">
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
	<div id="header">
		<div class="container">
			<h1><a href="/"><?php echo HW_GITHUB_LABEL; ?></a></h1>
			<h4>Find issues you can contribute to within <a href="<?php echo HW_GITHUB_URL; ?>"><?php echo HW_GITHUB_USER; ?>'s</a> open source projects.</h4>
		</div>
	</div>
	<div class="container">
		<div class="row">
			<div class="col-sm-3 hidden-xs" id="repos">
				<div>
					<h3>Projects</h3>
				</div>
			</div>
			<div class="col-sm-9" id="issues">
				<div class="row">
					<div class="col-sm-6">
						<h3>Issues</h3>
					</div>
					<div class="col-sm-6">
						<form class="form-inline" method="post">
							<input id="hashvalue" name="hashvalue" type="hidden" value="All" />
							<div class="form-group">
								<label for="sort">Sort by</label>
								<select class="form-control input-sm" name="sort" id="sort">
									<option value="updated_desc" <?php if($sort_order == 'updated_desc'){echo 'selected="selected"';} ?>>Recently updated</option>
									<option value="updated_asc" <?php if($sort_order == 'updated_asc'){echo 'selected="selected"';} ?>>Least recently updated</option>
									<option value="created_desc" <?php if($sort_order == 'created_desc'){echo 'selected="selected"';} ?>>Newest</option>
									<option value="created_asc" <?php if($sort_order == 'created_asc'){echo 'selected="selected"';} ?>>Oldest</option>
									<option value="comments_desc" <?php if($sort_order == 'comments_desc'){echo 'selected="selected"';} ?>>Most commented</option>
									<option value="comments_asc" <?php if($sort_order == 'comments_asc'){echo 'selected="selected"';} ?>>Least commented</option>
								</select>
							</div>
						</form>
					</div>
				</div>
				<blockquote>
					<p></p>
				</blockquote>					
				<div class="list-group">
					<?php
					foreach($issues as $issue){
						$repo_name = get_repo_name($issue['html_url']);
					?>
					<div class="list-group-item <?php echo $repo_name; ?>">
						<div class="row">
							<div class="col-sm-1">
								<a href="<?php echo $issue["user"]["html_url"]; ?>" target="_blank"><img src="<?php echo $issue["user"]["avatar_url"]; ?>&amp;s=75" class="hidden-xs img-responsive img-circle" alt="<?php echo $issue["user"]["login"]; ?>" title="<?php echo $issue["user"]["login"]; ?>"></a>
							</div>
							<div class="col-sm-11" >
								<h4 class="list-group-item-heading"><a href="<?php echo $issue["html_url"]; ?>" target="_blank"><?php echo htmlentities($issue["title"]); ?></a></h4>
								<p><?php echo limit_text($issue["body"], 35); ?></p>
								<ul class="list-inline text-muted small">
									<li><a href="<?php echo $repos_array[$repo_name]['url'] ?>" target="_blank"><span class="glyphicon glyphicon-hdd"></span> <?php echo HW_GITHUB_USER; ?>/<?php echo $repo_name; ?></a></li>
									<li><span class="glyphicon glyphicon-time"></span> Updated <time class="timeago" datetime="<?php echo $issue["updated_at"]; ?>"><?php echo $issue["updated_at"]; ?></time></li>
									<li><span class="glyphicon glyphicon-comment"></span> <strong><?php echo $issue["comments"]; ?></strong> <?php if ($issue["comments"] != 1){echo 'comments';}else{echo 'comment';} ?></li>
								</ul>
							</div>
						</div>
					</div>
					<?php 
						}            
					?>
				</div>
				<button id="issues_more" class="btn btn-default btn-lg btn-block">Load more...</button>
			</div>
		</div>
	</div>
	<div id="footer">
		<div class="container text-center small">
			<ul class="list-inline">
				<li><a href="#" data-toggle="modal" data-target="#about">About</a></li>
				<li><a href="<?php echo HW_ADMIN_URL; ?>">Github</a></li>
				<li><a href="mailto:<?php echo HW_ADMIN_EMAIL; ?>">Contact</a></li>
			</ul>
			<p><em>This is not a government sponsored website.</em></p>
		</div>
	</div>		
	<div id="about" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">About this website</h4>
				</div>
				<div class="modal-body">
					<p>This website aggregates all of the issues tagged with <em>"<?php echo HW_GITHUB_LABEL; ?>"</em> from within <?php echo HW_GITHUB_USER; ?>'s <a href="<?php echo HW_GITHUB_URL; ?>">public Github repositories</a>. The "<?php echo HW_GITHUB_LABEL; ?>" tag is often used by <?php echo HW_GITHUB_USER; ?> as a means of soliciting help from individuals that are interested in contributing to their projects.</p>
					<p>For more information about <?php echo HW_GITHUB_USER; ?>, please visit the <a href="<?php echo HW_WEBSITE_URL; ?>" target="_blank"><?php echo HW_WEBSITE_NAME; ?></a> website.</p>
				</div>
			</div>
		</div>
	</div>
	<a href="#" class="cd-top btn btn-default btn-lg"><span class="glyphicon glyphicon-chevron-up"></span></a>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timeago/1.4.3/jquery.timeago.min.js"></script>
	<script type="text/javascript">
		var hashvalue = "<?php if(isset($_POST["hashvalue"])){ echo $_POST["hashvalue"]; } ?>";
	</script>
	<script src="scripts.js"></script>
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	  ga('create', 'UA-69230191-1', 'auto');
	  ga('send', 'pageview');
	</script>
</body>
</html>